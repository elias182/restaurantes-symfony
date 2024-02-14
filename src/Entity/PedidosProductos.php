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
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pedidosProductos', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pedidos $pedido = null;

    #[ORM\ManyToOne(inversedBy: 'pedidosProductos', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Productos $producto = null;
    #[ORM\Column]
    private ?int $unidades = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUnidades(): ?int
    {
        return $this->unidades;
    }

    public function setUnidades(int $unidades): static
    {
        $this->unidades = $unidades;

        return $this;
    }
}
