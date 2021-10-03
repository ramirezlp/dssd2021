<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\SociedadAnonimaSocio;
use App\Repository\SociedadAnonimaRepository;

/**
 * @ORM\Entity(repositoryClass=SociedadAnonimaRepository::class)
 */
class SociedadAnonima
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
     * @ORM\Column(type="datetime")
     */
    private $fechaCreacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domicilioLegal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domicilioReal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pais;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $archivo;

    /**
     * @ORM\OneToMany(targetEntity=SociedadAnonimaSocio::class, mappedBy="sociedadAnonima")
     */
    private $socios;

    public function __construct(){
        $this->setFechaCreacion(new \DateTime());
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

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getDomicilioLegal(): ?string
    {
        return $this->domicilioLegal;
    }

    public function setDomicilioLegal(string $domicilioLegal): self
    {
        $this->domicilioLegal = $domicilioLegal;

        return $this;
    }

    public function getDomicilioReal(): ?string
    {
        return $this->domicilioReal;
    }

    public function setDomicilioReal(string $domicilioReal): self
    {
        $this->domicilioReal = $domicilioReal;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
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
     * Get the value of socios
     */ 
    public function getSocios()
    {
        return $this->socios;
    }


    /**
     * Get the value of archivo
     */ 
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Set the value of archivo
     *
     * @return  self
     */ 
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;

        return $this;
    }
}
