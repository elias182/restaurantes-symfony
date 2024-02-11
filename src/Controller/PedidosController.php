<?php

namespace App\Controller;

use App\Entity\Pedidos;
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

    #[Route('/new', name: 'app_pedidos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pedido = new Pedidos();
        $form = $this->createForm(PedidosType::class, $pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pedido);
            $entityManager->flush();

            return $this->redirectToRoute('app_pedidos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pedidos/new.html.twig', [
            'pedido' => $pedido,
            'form' => $form,
        ]);
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
