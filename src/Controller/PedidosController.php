<?php

namespace App\Controller;

use App\Entity\Pedidos;
use App\Entity\Productos;
use App\Entity\PedidosProductos;
use App\Form\PedidosType;
use App\Repository\PedidosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
#[Route('/pedidos')]
class PedidosController extends AbstractController
{
    private $mailer;
    private $authorizationChecker;
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, MailerInterface $mailer)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
    }
    #[Route('/', name: 'app_pedidos_index', methods: ['GET'])]
    public function index(PedidosRepository $pedidosRepository): Response
    {
        // Obtener el usuario actual
        $user = $this->getUser();

        // Verificar si el usuario está autenticado
        if (!$user) {
            // Enviar una respuesta de error o redirigir al usuario a la página de inicio de sesión
            // Por ejemplo:
            throw $this->createAccessDeniedException('Debes iniciar sesión para ver tus pedidos.');
        }

        // Verificar si el usuario es administrador
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            // Si el usuario es administrador, obtener todos los pedidos
            $pedidos = $pedidosRepository->findAll();
        } else {
            // Si el usuario no es administrador, obtener los pedidos del usuario actual
            $pedidos = $pedidosRepository->findBy(['restaurante' => $user]);
        }

        return $this->render('pedidos/index.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/create', name: 'app_pedidos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtener el contenido del carrito de la sesión
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        // Obtener el usuario actual (restaurante)
        $usuario = $this->getUser(); // Suponiendo que el usuario representa al restaurante

        // Crear un nuevo objeto Pedido
        $pedido = new Pedidos();
        $pedido->setFecha(new \DateTime()); // Establecer la fecha del pedido
        $pedido->setEnviado(0); // Marcar el pedido como no enviado (0)
        $pedido->setRestaurante($usuario); // Establecer el usuario actual como el restaurante asociado al pedido

        // Variable para almacenar el detalle del pedido
        $detallePedido = '';
        // Variable para almacenar el precio total del pedido
        $precioTotal = 0;

        // Iterar sobre los elementos del carrito
        foreach ($carrito as $item) {
            $productoId = $item['producto']; // ID del producto
            $cantidad = $item['cantidad']; // Cantidad del producto en el carrito

            // Obtener el producto desde la base de datos
            $producto = $entityManager->getRepository(Productos::class)->find($productoId);

            // Verificar si el producto existe
            if ($producto) {
                // Verificar si la cantidad solicitada es mayor que el stock disponible
                if ($cantidad > $producto->getStock()) {
                    continue;
                }

                // Calcular el precio del producto
                $precioProducto = $producto->getPrecio() * $cantidad;
                // Sumar el precio del producto al precio total del pedido
                $precioTotal += $precioProducto;

                // Crear una instancia de PedidosProductos
                $pedidosProducto = new PedidosProductos();
                $pedidosProducto->setPedido($pedido);
                $pedidosProducto->setProducto($producto);
                $pedidosProducto->setUnidades($cantidad);

                // Agregar el pedidosProducto al pedido
                $pedido->addPedidosProducto($pedidosProducto);

                // Persistir el pedidosProducto
                $entityManager->persist($pedidosProducto);

                // Reducir el stock del producto
                $producto->setStock($producto->getStock() - $cantidad);
                $entityManager->persist($producto);

                // Agregar detalles del producto al detalle del pedido
                $detallePedido .= sprintf("Producto: %s - Cantidad: %s - Precio: %s €\n", $producto->getNombre(), $cantidad, $precioProducto);
            } else {
                // Manejar el caso en el que el producto no se encuentre en la base de datos
                // Puedes lanzar una excepción, registrar un error o simplemente ignorar el producto
                // En este ejemplo, ignoraremos el producto y continuaremos con el siguiente
                continue;
            }
        }

        // Persistir el pedido
        $entityManager->persist($pedido);
        $entityManager->flush();

        // Enviar un correo electrónico al usuario actual con los detalles del pedido
        $email = (new Email())
            ->from('pruebasphpdanii@gmail.com')
            ->to($usuario->getEmail()) // El destinatario es el correo electrónico del usuario actual
            ->subject('Nuevo pedido creado')
            ->html('<p>Se ha creado un nuevo pedido.</p><p>Detalles del pedido:</p><p>' . $detallePedido . '</p><p>Precio total del pedido: ' . $precioTotal . ' €</p>');

        $this->mailer->send($email);

        // Limpiar el carrito después de guardar el pedido
        $session->set('carrito', []);

        // Redirigir a la página de pedidos index
        return $this->redirectToRoute('app_pedidos_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_pedidos_show', methods: ['GET'])]
    public function show(Pedidos $pedido): Response
    {
        return $this->render('pedidos/show.html.twig', [
            'pedido' => $pedido,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pedidos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pedidos $pedido, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PedidosType::class, $pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pedidos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pedidos/edit.html.twig', [
            'pedido' => $pedido,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pedidos_delete', methods: ['POST'])]
    public function delete(Request $request, Pedidos $pedido, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pedido->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pedido);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pedidos_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/cancelar', name: 'app_pedidos_cancelar', methods: ['POST'])]
    public function cancelarPedido(Request $request, Pedidos $pedido, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Verificar si el usuario es administrador
        
        // Cambiar el estado del pedido a cancelado (2)
        $pedido->setEnviado(2);
        $entityManager->flush();

        $this->addFlash('success', 'Pedido cancelado exitosamente.');

        return $this->redirectToRoute('app_pedidos_index');
    }

    #[Route('/{id}/confirmar', name: 'app_pedidos_confirmar', methods: ['POST'])]
    public function confirmarPedido(Request $request, Pedidos $pedido, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Verificar si el usuario es administrador
        
        // Cambiar el estado del pedido a confirmado (1)
        $pedido->setEnviado(1);
        $entityManager->flush();

        $this->addFlash('success', 'Pedido confirmado exitosamente.');

        return $this->redirectToRoute('app_pedidos_index');
    }
}
