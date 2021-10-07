<?php

namespace App\Entity;

use App\Entity\PaisEstado;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\SociedadAnonimaSocio;
use App\Repository\SociedadAnonimaRepository;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(type="datetime", nullable=true)
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
    private $archivo;

    /**
     * @ORM\OneToMany(targetEntity=SociedadAnonimaSocio::class, mappedBy="sociedadAnonima")
     */
    private $socios;

    /** 
     * @var \Doctrine\Common\Collections\Collection|PaisEstado[]
     * @ORM\ManyToMany(targetEntity="App\Entity\PaisEstado", inversedBy="sociedades")
     *
     * @ORM\JoinTable(
     *  name="pais_estado_sociedad_anonima",
     *  joinColumns={
     *      @ORM\JoinColumn(name="sociedad_anonima_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="pais_estado_id", referencedColumnName="id")
     *  }
     * )
     */
    private $paisesEstados;
        

    public function __construct(){
        $this->setFechaCreacion(new \DateTime());
        $this->socios = new ArrayCollection();
        $this->paisesEstados = new ArrayCollection();
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

    /**
     * Get the value of socios
     */
    public function getSocios()
    {
        return $this->socios;
    }


    /**
     * Set the value of socios
     */
    public function setSocios($socios): self
    {
        $this->socios = $socios;

        return $this;
    }

    /**
     * Get the value of paisesEstados
     */ 
    public function getPaisesEstados()
    {
        return $this->paisesEstados;
    }

    public function addPaisesEstados(PaisEstado $paisEstado): self
    {
        if (!$this->paisesEstados->contains($paisEstado)) {
            $this->paisesEstados[] = $paisEstado;
            $paisEstado->addSociedades($this);
        }
        return $this;
    }
    public function removePaisesEstados(PaisEstado $paisEstado): self
    {
        if ($this->paisesEstados->removeElement($paisEstado)) {
            $paisEstado->removeSociedades($this);
        }
        return $this;
    }

    /**
     * Set the value of paisesEstados
     *
     * @return  self
     */ 
    public function setPaisesEstados($paisesEstados): self
    {
        $this->paisesEstados = $paisesEstados;

        return $this;
    }
}
