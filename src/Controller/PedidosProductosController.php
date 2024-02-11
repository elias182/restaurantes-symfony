<?php

namespace App\Controller;

use App\Entity\PedidosProductos;
use App\Form\PedidosProductosType;
use App\Repository\PedidosProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pedidos/productos')]
class PedidosProductosController extends AbstractController
{
    #[Route('/', name: 'app_pedidos_productos_index', methods: ['GET'])]
    public function index(PedidosProductosRepository $pedidosProductosRepository): Response
    {
        return $this->render('pedidos_productos/index.html.twig', [
            'pedidos_productos' => $pedidosProductosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pedidos_productos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pedidosProducto = new PedidosProductos();
        $form = $this->createForm(PedidosProductosType::class, $pedidosProducto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pedidosProducto);
            $entityManager->flush();

            return $this->redirectToRoute('app_pedidos_productos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pedidos_productos/new.html.twig', [
            'pedidos_producto' => $pedidosProducto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pedidos_productos_show', methods: ['GET'])]
    public function show(PedidosProductos $pedidosProducto): Response
    {
        return $this->render('pedidos_productos/show.html.twig', [
            'pedidos_producto' => $pedidosProducto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pedidos_productos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PedidosProductos $pedidosProducto, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PedidosProductosType::class, $pedidosProducto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pedidos_productos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pedidos_productos/edit.html.twig', [
            'pedidos_producto' => $pedidosProducto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pedidos_productos_delete', methods: ['POST'])]
    public function delete(Request $request, PedidosProductos $pedidosProducto, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pedidosProducto->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pedidosProducto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pedidos_productos_index', [], Response::HTTP_SEE_OTHER);
    }
}
