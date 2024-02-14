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

#[Route('/pedidos')]
class PedidosController extends AbstractController
{
    #[Route('/', name: 'app_pedidos_index', methods: ['GET'])]
    public function index(PedidosRepository $pedidosRepository): Response
    {
        return $this->render('pedidos/index.html.twig', [
            'pedidos' => $pedidosRepository->findAll(),
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

    // Iterar sobre los elementos del carrito
    foreach ($carrito as $item) {
        $productoId = $item['producto']; // ID del producto
        $cantidad = $item['cantidad']; // Cantidad del producto en el carrito
    
        // Obtener el producto desde la base de datos
        $producto = $entityManager->getRepository(Productos::class)->find($productoId);
    
        // Verificar si el producto existe
        if ($producto) {
            // Crear una instancia de PedidosProductos
            $pedidosProducto = new PedidosProductos();
            $pedidosProducto->setPedido($pedido);
            $pedidosProducto->setProducto($producto);
            $pedidosProducto->setUnidades($cantidad);
    
            // Agregar el pedidosProducto al pedido
            $pedido->addPedidosProducto($pedidosProducto);
            
            // Persistir el pedidosProducto
            $entityManager->persist($pedidosProducto);
        } else {
            // Manejar el caso en el que el producto no se encuentre en la base de datos
            // Puedes lanzar una excepción, registrar un error o simplemente ignorar el producto
            // En este ejemplo, ignoraremos el producto y continuaremos con el siguiente
            continue;
        }
    }

    // Persistir el pedido y los productos en la base de datos
    $entityManager->persist($pedido);
    $entityManager->flush();

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
}
