<?php

namespace App\Entity;

use App\Repository\ProductosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductosRepository::class)]
class Productos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $codProd = null;

    #[ORM\Column(length: 45)]
    private ?string $nombre = null;

    #[ORM\Column(length: 90)]
    private ?string $descripcion = null;

    #[ORM\Column]
    private ?float $peso = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\OneToMany(targetEntity: PedidosProductos::class, mappedBy: 'producto')]
    private Collection $producto;

    #[ORM\ManyToOne(inversedBy: 'categoria')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorias $categoria = null;

    public function __construct()
    {
        $this->producto = new ArrayCollection();
    }

    

    public function getCodProd(): ?int
    {
        return $this->codProd;
    }

    public function setCodProd(int $codProd): static
    {
        $this->codProd = $codProd;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function setPeso(float $peso): static
    {
        $this->peso = $peso;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, PedidosProductos>
     */
    public function getProducto(): Collection
    {
        return $this->producto;
    }

    public function addProducto(PedidosProductos $producto): static
    {
        if (!$this->producto->contains($producto)) {
            $this->producto->add($producto);
            $producto->setProducto($this);
        }

        return $this;
    }

    public function removeProducto(PedidosProductos $producto): static
    {
        if ($this->producto->removeElement($producto)) {
            // set the owning side to null (unless already changed)
            if ($producto->getProducto() === $this) {
                $producto->setProducto(null);
            }
        }

        return $this;
    }

    public function getCategoria(): ?Categorias
    {
        return $this->categoria;
    }

    public function setCategoria(?Categorias $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }
}
