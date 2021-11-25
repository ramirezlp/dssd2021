<?php

namespace App\Entity;

use App\Repository\HistoricoEstadoSociedadAnonimaRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoricoEstadoSociedadAnonimaRepository::class)
 */
class HistoricoEstadoSociedadAnonima
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
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity=SociedadAnonima::class)
     * @ORM\JoinColumn(nullable=false, onDelete="NO ACTION")
     */
    private $sociedadAnonima;

     /**
     * @ORM\Column(type="datetime")
     */
    protected $fechaCreacion;

    public function __construct()
    {
        $this->fechaCreacion= new DateTime();
    }

    public function getId(): ?int
    {    
        return $this->id;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
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
     *
     * @return  self
     */ 
    public function setSociedadAnonima($sociedadAnonima)
    {
        $this->sociedadAnonima = $sociedadAnonima;

        return $this;
    }

    /**
     * Get the value of fechaCreacion
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

}
