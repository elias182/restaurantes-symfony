<?php

namespace App\Entity;

use App\Repository\RestaurantesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantesRepository::class)]
class Restaurantes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $codRes = null;

    #[ORM\Column(length: 90)]
    private ?string $correo = null;

    #[ORM\Column(length: 45)]
    private ?string $clave = null;

    #[ORM\Column(length: 45)]
    private ?string $pais = null;

    #[ORM\Column]
    private ?int $cp = null;

    #[ORM\Column(length: 45)]
    private ?string $ciudad = null;

    #[ORM\Column(length: 200)]
    private ?string $direccion = null;

    #[ORM\OneToMany(targetEntity: Pedidos::class, mappedBy: 'restaurante')]
    private Collection $restaurante;

    public function __construct()
    {
        $this->restaurante = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodRes(): ?int
    {
        return $this->codRes;
    }

    public function setCodRes(int $codRes): static
    {
        $this->codRes = $codRes;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): static
    {
        $this->correo = $correo;

        return $this;
    }

    public function getClave(): ?string
    {
        return $this->clave;
    }

    public function setClave(string $clave): static
    {
        $this->clave = $clave;

        return $this;
    }

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(string $pais): static
    {
        $this->pais = $pais;

        return $this;
    }

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(int $cp): static
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCiudad(): ?string
    {
        return $this->ciudad;
    }

    public function setCiudad(string $ciudad): static
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * @return Collection<int, Pedidos>
     */
    public function getRestaurante(): Collection
    {
        return $this->restaurante;
    }

    public function addRestaurante(Pedidos $restaurante): static
    {
        if (!$this->restaurante->contains($restaurante)) {
            $this->restaurante->add($restaurante);
            $restaurante->setRestaurante($this);
        }

        return $this;
    }

    public function removeRestaurante(Pedidos $restaurante): static
    {
        if ($this->restaurante->removeElement($restaurante)) {
            // set the owning side to null (unless already changed)
            if ($restaurante->getRestaurante() === $this) {
                $restaurante->setRestaurante(null);
            }
        }

        return $this;
    }
}
