<?php

namespace App\Entity;

use App\Repository\CategoriasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriasRepository::class)]
class Categorias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $codCat = null;

    #[ORM\Column(length: 45)]
    private ?string $Nombre = null;

    #[ORM\Column(length: 200)]
    private ?string $descripcion = null;

    #[ORM\OneToMany(targetEntity: Productos::class, mappedBy: 'categoria')]
    private Collection $categoria;

    public function __construct()
    {
        $this->categoria = new ArrayCollection();
    }

    public function getCodCat(): ?int
    {
        return $this->codCat;
    }

    public function setCodCat(int $codCat): static
    {
        $this->codCat = $codCat;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->Nombre;
    }

    public function setNombre(string $Nombre): static
    {
        $this->Nombre = $Nombre;

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

    /**
     * @return Collection<int, Productos>
     */
    public function getCategoria(): Collection
    {
        return $this->categoria;
    }

    public function addCategorium(Productos $categorium): static
    {
        if (!$this->categoria->contains($categorium)) {
            $this->categoria->add($categorium);
            $categorium->setCategoria($this);
        }

        return $this;
    }

    public function removeCategorium(Productos $categorium): static
    {
        if ($this->categoria->removeElement($categorium)) {
            // set the owning side to null (unless already changed)
            if ($categorium->getCategoria() === $this) {
                $categorium->setCategoria(null);
            }
        }

        return $this;
    }
}
