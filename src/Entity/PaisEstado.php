<?php

namespace App\Entity;

use App\Entity\SociedadAnonima;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PaisEstadoRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=PaisEstadoRepository::class)
 */
class PaisEstado
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pais;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SociedadAnonima", inversedBy="paisesEstados")
     */
    private $sociedades;

    public function __construct(){
        $this->sociedades = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(string $pais): self
    {
        $this->pais = $pais;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get the value of sociedades
     */ 
    public function getSociedades()
    {
        return $this->sociedades;
    }

    /**
     * Set the value of sociedades
     *
     * @return  self
     */ 
    public function setSociedades($sociedades)
    {
        $this->sociedades = $sociedades;

        return $this;
    }

    public function addSociedades(SociedadAnonima $sociedadAnonima): self
    {
        if (!$this->sociedades->contains($sociedadAnonima)) {
            $this->sociedades[] = $sociedadAnonima;
            $sociedadAnonima->addPaisesEstados($this);
        }
        return $this;
    }
    public function removeSociedades(SociedadAnonima $sociedadAnonima): self
    {
        if ($this->sociedades->removeElement($sociedadAnonima)) {
            $sociedadAnonima->removePaisesEstados($this);
        }
        return $this;
    }
}
