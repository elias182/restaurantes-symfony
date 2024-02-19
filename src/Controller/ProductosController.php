<?php

namespace App\Controller;
use App\Entity\PedidosProductos;
use App\Entity\Productos;
use App\Form\ProductosType;
use App\Repository\ProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/productos')]
class ProductosController extends AbstractController
{
    #[Route('/', name: 'app_productos_index', methods: ['GET'])]
    public function index(ProductosRepository $productosRepository): Response
    {
        return $this->render('productos/index.html.twig', [
            'productos' => $productosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_productos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $producto = new Productos();
        $form = $this->createForm(ProductosType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Manejo de la carga de imagen
            $imagenFile = $form['imagen']->getData();
            if ($imagenFile) {
                // Directorio donde se guardarán las imágenes
                $directorioImagenes = $this->getParameter('imagenes_directorio_productos');
                
                // Verificar si el directorio existe, si no, crearlo
                if (!$filesystem->exists($directorioImagenes)) {
                    $filesystem->mkdir($directorioImagenes, 0777);
                }

                // Generar un nombre único para el archivo de imagen
                $nombreArchivo = md5(uniqid()) . '.' . $imagenFile->guessExtension();
                
                // Mover la imagen al directorio de imágenes
                try {
                    $imagenFile->move(
                        $directorioImagenes,
                        $nombreArchivo
                    );
                } catch (FileException $e) {
                    // Manejar excepción si la subida falla
                }

                // Asignar el nombre del archivo de imagen al producto
                $producto->setImagen($nombreArchivo);
            }else {
                $producto->setImagen('default.jpg'); 
            }

            $entityManager->persist($producto);
            $entityManager->flush();

            return $this->redirectToRoute('app_productos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('productos/new.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_productos_show', methods: ['GET'])]
    public function show(Productos $producto): Response
    {
        return $this->render('productos/show.html.twig', [
            'producto' => $producto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_productos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Productos $producto, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $form = $this->createForm(ProductosType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Manejo de la carga de imagen
            $imagenFile = $form['imagen']->getData();
            if ($imagenFile) {
                // Directorio donde se guardarán las imágenes
                $directorioImagenes = $this->getParameter('imagenes_directorio_productos');
                
                // Verificar si el directorio existe, si no, crearlo
                if (!$filesystem->exists($directorioImagenes)) {
                    $filesystem->mkdir($directorioImagenes, 0777);
                }

                // Generar un nombre único para el archivo de imagen
                $nombreArchivo = md5(uniqid()) . '.' . $imagenFile->guessExtension();
                
                // Mover la imagen al directorio de imágenes
                try {
                    $imagenFile->move(
                        $directorioImagenes,
                        $nombreArchivo
                    );
                } catch (FileException $e) {
                    // Manejar excepción si la subida falla
                }

                // Eliminar la imagen anterior si existe
                $imagenAnterior = $producto->getImagen();
                if ($imagenAnterior) {
                    $rutaImagenAnterior = $directorioImagenes . '/' . $imagenAnterior;
                    if ($filesystem->exists($rutaImagenAnterior)) {
                        $filesystem->remove($rutaImagenAnterior);
                    }
                }

                // Asignar el nuevo nombre del archivo de imagen al producto
                $producto->setImagen($nombreArchivo);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_productos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('productos/edit.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_productos_delete', methods: ['POST'])]
public function delete(Request $request, Productos $producto, EntityManagerInterface $entityManager): Response
{
    // Obtener todos los registros de PedidoProducto asociados al producto
    $pedidoProductos = $entityManager->getRepository(PedidosProductos::class)->findBy(['producto' => $producto]);
    
    // Iterar sobre cada registro de PedidoProducto y eliminarlo
    foreach ($pedidoProductos as $pedidoProducto) {
        $entityManager->remove($pedidoProducto);
    }

    // Eliminar el producto
    $entityManager->remove($producto);
    $entityManager->flush();

    return $this->redirectToRoute('app_productos_index', [], Response::HTTP_SEE_OTHER);
}
}
