<?php

namespace App\Entity;

use App\Repository\PedidosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidosRepository::class)]
class Pedidos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?int $enviado = null;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurantes $restaurante = null;

    #[ORM\OneToMany(targetEntity: PedidosProductos::class, mappedBy: 'pedido')]
    private Collection $pedidosProductos;

    public function __construct()
    {
        $this->pedidosProductos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEnviado(): ?int
    {
        return $this->enviado;
    }

    public function setEnviado(int $enviado): static
    {
        $this->enviado = $enviado;

        return $this;
    }

    public function getRestaurante(): ?Restaurantes
    {
        return $this->restaurante;
    }

    public function setRestaurante(?Restaurantes $restaurante): static
    {
        $this->restaurante = $restaurante;

        return $this;
    }

    /**
     * @return Collection<int, PedidosProductos>
     */
    public function getPedidosProductos(): Collection
    {
        return $this->pedidosProductos;
    }

    public function addPedidosProducto(PedidosProductos $pedidosProducto): static
    {
        if (!$this->pedidosProductos->contains($pedidosProducto)) {
            $this->pedidosProductos->add($pedidosProducto);
            $pedidosProducto->setPedido($this);
        }

        return $this;
    }

    public function removePedidosProducto(PedidosProductos $pedidosProducto): static
    {
        if ($this->pedidosProductos->removeElement($pedidosProducto)) {
            // set the owning side to null (unless already changed)
            if ($pedidosProducto->getPedido() === $this) {
                $pedidosProducto->setPedido(null);
            }
        }

        return $this;
    }
}
