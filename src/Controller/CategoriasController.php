<?php

namespace App\Controller;

use App\Entity\Categorias;
use App\Form\CategoriasType;
use App\Repository\CategoriasRepository;
use App\Repository\ProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/categorias')]
class CategoriasController extends AbstractController
{
    #[Route('/', name: 'app_categorias_index', methods: ['GET'])]
    public function index(CategoriasRepository $categoriasRepository): Response
    {
        return $this->render('categorias/index.html.twig', [
            'categorias' => $categoriasRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorias_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
{
    $categoria = new Categorias();
    $form = $this->createForm(CategoriasType::class, $categoria);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Manejo de la carga de imagen
        $imagenFile = $form['imagen']->getData();
        if ($imagenFile) {
            $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
            // Cambia esto según tu necesidad para guardar la imagen en un directorio adecuado
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

            // Verificar si el directorio existe, si no, crearlo
            $directorioImagenes = $this->getParameter('imagenes_directorio');
            if (!$filesystem->exists($directorioImagenes)) {
                $filesystem->mkdir($directorioImagenes, 0777);
            }

            try {
                $imagenFile->move(
                    $directorioImagenes, // Directorio de destino
                    $newFilename
                );
            } catch (FileException $e) {
                // Manejar excepción si la subida falla
            }
            $categoria->setImagen($newFilename);
        }else {
            $categoria->setImagen('default.jpg'); 
        }

        $entityManager->persist($categoria);
        $entityManager->flush();

        return $this->redirectToRoute('app_categorias_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('categorias/new.html.twig', [
        'categoria' => $categoria,
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_categorias_show', methods: ['GET'])]
    public function show(Categorias $categoria): Response
    {
        return $this->render('categorias/show.html.twig', [
            'categoria' => $categoria,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorias_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorias $categoria, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriasType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Manejo de la carga de imagen
            $imagenFile = $form['imagen']->getData();
            if ($imagenFile) {
                $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Cambia esto según tu necesidad para guardar la imagen en un directorio adecuado
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();
                try {
                    $imagenFile->move(
                        $this->getParameter('imagenes_directorio'), // Directorio de destino
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Manejar excepción si la subida falla
                }
                $categoria->setImagen($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_categorias_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorias/edit.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_categorias_delete', methods: ['POST'])]
    public function delete(Request $request, Categorias $categoria, EntityManagerInterface $entityManager): Response
    {
        $productos = $categoria->getProductos();
    
        // Verificar si la categoría tiene productos asociados
        if (!$productos->isEmpty()) {
            $this->addFlash('error', 'No se puede eliminar la categoría porque contiene productos asociados.');
        } else {
            // Si la categoría no tiene productos asociados, eliminarla
            if ($this->isCsrfTokenValid('delete'.$categoria->getId(), $request->request->get('_token'))) {
                $entityManager->remove($categoria);
                $entityManager->flush();
                $this->addFlash('success', 'Categoría eliminada correctamente.');
            }
        }
    
        return $this->redirectToRoute('app_categorias_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/productos', name: 'productos_por_categoria', methods: ['GET'])]
    public function productosPorCategoria(?Categorias $categoria, ProductosRepository $productosRepository): Response
    {
        if (!$categoria) {
            // Manejar el caso cuando no se proporciona una categoría válida, por ejemplo, redirigir a la página de categorías
            return $this->redirectToRoute('app_categorias_index');
        }
    
        $productos = $productosRepository->findByCategoria($categoria);
    
        return $this->render('categorias/productos_por_categoria.html.twig', [
            'categoria' => $categoria,
            'productos' => $productos,
        ]);
    }
}
