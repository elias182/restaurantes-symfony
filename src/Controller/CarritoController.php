<?php
// src/Controller/CarritoController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Productos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CarritoController extends AbstractController
{
    #[Route('/carrito', name: 'carrito')]
    public function index(PedidosRepository $pedidosRepository): Response
{
    // Obtener el usuario actual
    $user = $this->getUser();

    // Verificar si el usuario está autenticado
    if ($user) {
        // Obtener los pedidos del usuario actual
        $pedidos = $user->getPedidos();
    } else {
        // Enviar una respuesta de error o redirigir al usuario a la página de inicio de sesión
        // Por ejemplo:
        throw $this->createAccessDeniedException('Debes iniciar sesión para ver tus pedidos.');
    }

    return $this->render('pedidos/index.html.twig', [
        'pedidos' => $pedidos,
    ]);
}

    #[Route('/agregar/{producto}', name: 'agregar_producto')]
    public function agregarProducto(Request $request, $producto): Response
    {
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        // Agregar el producto al carrito
        $carrito[] = $producto;
        $session->set('carrito', $carrito);

        return $this->redirectToRoute('carrito');
    }

    #[Route('/eliminar/{indice}', name: 'eliminar_producto')]
    public function eliminarProducto(Request $request, $indice): Response
    {
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        // Eliminar el producto del carrito
        if (isset($carrito[$indice])) {
            unset($carrito[$indice]);
            $session->set('carrito', $carrito);
        }

        return $this->redirectToRoute('carrito');
    }
    #[Route('/agregar-al-carrito', name: 'agregar_al_carrito', methods: ['POST'])]
    public function agregarAlCarrito(Request $request, EntityManagerInterface $entityManager): Response
{
    // Obtener el ID del producto de la solicitud
    $productoId = $request->request->get('producto_id');

    // Obtener el producto desde la base de datos
    $producto = $entityManager->getRepository(Productos::class)->find($productoId);

    // Verificar si el producto existe
    if (!$producto) {
        throw $this->createNotFoundException('El producto no existe');
    }

    // Obtener el carrito del usuario desde la sesión
    $session = $request->getSession();
    $carrito = $session->get('carrito', []);

    // Verificar si el producto ya está en el carrito
    $productoEnCarrito = false;
    foreach ($carrito as &$item) {
        if ($item['producto']->getId() === $producto->getId()) {
            // Verificar si la cantidad en el carrito es menor que el stock disponible
            if ($item['cantidad'] < $producto->getStock()) {
                $item['cantidad'] += 1;
            }
            $productoEnCarrito = true;
            break;
        }
    }

    // Si el producto no está en el carrito y la cantidad en el carrito es menor que el stock, agregarlo con cantidad igual a uno
    if (!$productoEnCarrito && $producto->getStock() > 0) {
        $carrito[] = ['producto' => $producto, 'cantidad' => 1];
    }

    // Actualizar el carrito en la sesión del usuario
    $session->set('carrito', $carrito);

    // Redirigir al usuario de vuelta al índice del carrito
    return $this->redirectToRoute('carrito');
}

#[Route('/aumentar/{indice}', name: 'aumentar', methods: ['GET'])]
public function aumentar(Request $request, $indice)
{
    // Obtener el carrito del usuario desde la sesión
    $session = $request->getSession();
    $carrito = $session->get('carrito', []);

    // Verificar si el índice dado es válido
    if (!isset($carrito[$indice])) {
        throw $this->createNotFoundException('El índice del producto en el carrito no es válido.');
    }

    // Obtener el producto y la cantidad actual en el carrito
    $producto = $carrito[$indice]['producto'];
    $cantidadActual = $carrito[$indice]['cantidad'];

    // Comprobar si la cantidad en el carrito ya es igual al stock disponible
    if ($cantidadActual == $producto->getStock()) {
        // Redirigir de vuelta al carrito sin hacer cambios
        return $this->redirectToRoute('carrito');
    }

    // Aumentar la cantidad del producto en el carrito en uno
    $carrito[$indice]['cantidad']++;

    // Actualizar el carrito en la sesión del usuario
    $session->set('carrito', $carrito);

    // Redirigir de vuelta al carrito
    return $this->redirectToRoute('carrito');
}

#[Route('/disminuir/{indice}', name: 'disminuir', methods: ['GET'])]
public function disminuir(Request $request, $indice)
{
    // Obtener el carrito del usuario desde la sesión
    $session = $request->getSession();
    $carrito = $session->get('carrito', []);

    // Verificar si el índice dado es válido
    if (!isset($carrito[$indice])) {
        throw $this->createNotFoundException('El índice del producto en el carrito no es válido.');
    }

    // Si la cantidad es mayor que 1, simplemente disminuye la cantidad en uno
    if ($carrito[$indice]['cantidad'] > 1) {
        $carrito[$indice]['cantidad']--;
    }
    // Si la cantidad es 1, elimina completamente el producto del carrito
    else {
        unset($carrito[$indice]);
    }

    // Actualizar el carrito en la sesión del usuario
    $session->set('carrito', $carrito);

    // Redirigir de vuelta al carrito
    return $this->redirectToRoute('carrito');
}
}