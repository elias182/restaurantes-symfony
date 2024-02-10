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
    private ?int $codPed = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?int $enviado = null;

    #[ORM\ManyToOne(inversedBy: 'restaurante')]
    #[ORM\JoinColumn(nullable: false)]
    private ?restaurantes $restaurante = null;

    #[ORM\OneToMany(targetEntity: PedidosProductos::class, mappedBy: 'pedido')]
    private Collection $pedido;

    public function __construct()
    {
        $this->pedido = new ArrayCollection();
    }

    public function getCodPed(): ?int
    {
        return $this->codPed;
    }

    public function setCodPed(int $codPed): static
    {
        $this->codPed = $codPed;

        return $this;
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

    public function getRestaurante(): ?restaurantes
    {
        return $this->restaurante;
    }

    public function setRestaurante(?restaurantes $restaurante): static
    {
        $this->restaurante = $restaurante;

        return $this;
    }

    /**
     * @return Collection<int, PedidosProductos>
     */
    public function getPedido(): Collection
    {
        return $this->pedido;
    }

    public function addPedido(PedidosProductos $pedido): static
    {
        if (!$this->pedido->contains($pedido)) {
            $this->pedido->add($pedido);
            $pedido->setPedido($this);
        }

        return $this;
    }

    public function removePedido(PedidosProductos $pedido): static
    {
        if ($this->pedido->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getPedido() === $this) {
                $pedido->setPedido(null);
            }
        }

        return $this;
    }
}
