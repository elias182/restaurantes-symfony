<?php

namespace App\Entity;

use App\Repository\PedidosProductosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidosProductosRepository::class)]
class PedidosProductos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $codPedProd = null;

    #[ORM\Column]
    private ?int $unidades = null;

    #[ORM\ManyToOne(inversedBy: 'pedido')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pedidos $pedido = null;

    #[ORM\ManyToOne(inversedBy: 'producto')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Productos $producto = null;


    public function getCodPedProd(): ?int
    {
        return $this->codPedProd;
    }

    public function setCodPedProd(int $codPedProd): static
    {
        $this->codPedProd = $codPedProd;

        return $this;
    }

    public function getUnidades(): ?int
    {
        return $this->unidades;
    }

    public function setUnidades(int $unidades): static
    {
        $this->unidades = $unidades;

        return $this;
    }

    public function getPedido(): ?Pedidos
    {
        return $this->pedido;
    }

    public function setPedido(?Pedidos $pedido): static
    {
        $this->pedido = $pedido;

        return $this;
    }

    public function getProducto(): ?Productos
    {
        return $this->producto;
    }

    public function setProducto(?Productos $producto): static
    {
        $this->producto = $producto;

        return $this;
    }
}
