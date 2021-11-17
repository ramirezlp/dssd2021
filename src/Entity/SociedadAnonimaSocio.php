<?php

namespace App\Entity;

use App\Entity\Socio;
use App\Entity\SociedadAnonima;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SociedadAnonimaSocioRepository;

/**
 * @ORM\Entity(repositoryClass=SociedadAnonimaSocioRepository::class)
 */
class SociedadAnonimaSocio
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SociedadAnonima::class, inversedBy = "socios",  cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="NO ACTION")
     */
    private $sociedadAnonima;

    /**
     * @ORM\ManyToOne(targetEntity=Socio::class, inversedBy = "sociedadesAnonimas", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false,onDelete="NO ACTION")
     */
    private $socio;

    /**
     * @ORM\Column(type="integer")
     */
    private $porcentajeAporte;

    /**
     * @ORM\Column(type="boolean", nullable = true)
     */
    private $esRepresentanteLegal;

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the value of sociedadAnonima
     */
    public function getSociedadAnonima()
    {
        return $this->sociedadAnonima;
    }

    /**
     * Set the value of sociedadAnonima
     */
    public function setSociedadAnonima($sociedadAnonima): self
    {
        $this->sociedadAnonima = $sociedadAnonima;

        return $this;
    }

    /**
     * Get the value of socio
     */ 
    public function getSocio()
    {
        return $this->socio;
    }

    /**
     * Set the value of socio
     *
     * @return  self
     */ 
    public function setSocio($socio)
    {
        $this->socio = $socio;

        return $this;
    }

    /**
     * Get the value of porcentajeAporte
     */ 
    public function getPorcentajeAporte()
    {
        return $this->porcentajeAporte;
    }

    /**
     * Set the value of porcentajeAporte
     *
     * @return  self
     */ 
    public function setPorcentajeAporte($porcentajeAporte)
    {
        $this->porcentajeAporte = $porcentajeAporte;

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
