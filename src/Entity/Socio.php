<?php

namespace App\Entity;

use App\Repository\SocioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SocioRepository::class)
 */
class Socio
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
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apellido;

    /**
     * @ORM\OneToMany(targetEntity=SociedadAnonimaSocio::class, mappedBy="socio",cascade={"persist", "remove"})
     */
    private $sociedadesAnonimas;

    private $esRepresentanteLegal = false;
    
    private $porcentaje = 0;
    
    public function __construct(){
        $this->sociedadesAnonimas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get the value of sociedadesAnonimas
     */ 
    public function getSociedadesAnonimas()
    {
        return $this->sociedadesAnonimas;
    }

    /**
     * Get the value of porcentaje
     */ 
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set the value of porcentaje
     *
     * @return  self
     */ 
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get the value of esRepresentanteLegal
     */ 
    public function getEsRepresentanteLegal()
    {
        return $this->esRepresentanteLegal;
    }

    /**
     * Set the value of esRepresentanteLegal
     *
     * @return  self
     */ 
    public function setEsRepresentanteLegal($esRepresentanteLegal)
    {
        $this->esRepresentanteLegal = $esRepresentanteLegal;

        return $this;
    }
}
