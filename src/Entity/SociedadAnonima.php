<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\PaisEstado;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\SociedadAnonimaSocio;
use App\Form\SociedadAnonimaSocioType;
use App\Repository\SociedadAnonimaRepository;
use Symfony\Component\HttpFoundation\File\File;
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
     * @ORM\OneToMany(targetEntity=SociedadAnonimaSocio::class, mappedBy="sociedadAnonima", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $socios;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado = "PENDIENTE";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motivoRechazo = "No ingresado";

    /**
     * @ORM\Column(type="integer")
     */
    private $plazoCorreccion = 0;

    /**
     * @ORM\Column(type="integer", unique=true, nullable=true)
     */
    private $numeroExpediente;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $caseId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qr;


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

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true,onDelete="NO ACTION")
     */
    private $solicitante;
        

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

    public function addSocio(SociedadAnonimaSocio $socio): self
    {
        if (!$this->socios->contains($socio)) {
            $this->socios[] = $socio;
            $socio->setSociedadAnonima($this);
        }
        return $this;
    }
    public function removeSocio(SociedadAnonimaSocio $socio): self
    {
        if ($this->socios->removeElement($socio)) {
            $socio->setSociedadAnonima($this);
        }
        return $this;
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

    /**
     * Get the value of estado
     */ 
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set the value of estado
     *
     * @return  self
     */ 
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get the value of solicitante
     */ 
    public function getSolicitante()
    {
        return $this->solicitante;
    }

    /**
     * Set the value of solicitante
     *
     * @return  self
     */ 
    public function setSolicitante($solicitante)
    {
        $this->solicitante = $solicitante;

        return $this;
    }

    /**
     * Get the value of motivoRechazo
     */ 
    public function getMotivoRechazo()
    {
        return $this->motivoRechazo;
    }

    /**
     * Set the value of motivoRechazo
     *
     * @return  self
     */ 
    public function setMotivoRechazo($motivoRechazo)
    {
        $this->motivoRechazo = $motivoRechazo;

        return $this;
    }

    /**
     * Get the value of plazoCorreccion
     */
    public function getPlazoCorreccion()
    {
        return $this->plazoCorreccion;
    }

    /**
     * Set the value of plazoCorreccion
     */
    public function setPlazoCorreccion($plazoCorreccion): self
    {
        $this->plazoCorreccion = $plazoCorreccion;

        return $this;
    }

    /**
     * Get the value of numeroExpediente
     */ 
    public function getNumeroExpediente()
    {
        return $this->numeroExpediente;
    }

    /**
     * Set the value of numeroExpediente
     *
     * @return  self
     */ 
    public function setNumeroExpediente($numeroExpediente)
    {
        $this->numeroExpediente = $numeroExpediente;

        return $this;
    }

    /**
     * Get the value of caseId
     */ 
    public function getCaseId()
    {
        return $this->caseId;
    }

    /**
     * Set the value of caseId
     *
     * @return  self
     */ 
    public function setCaseId($caseId)
    {
        $this->caseId = $caseId;

        return $this;
    }

    /**
     * Get the value of hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set the value of hash
     */
    public function setHash($hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get the value of qr
     */
    public function getQr()
    {
        return $this->qr;
    }

    /**
     * Set the value of qr
     */
    public function setQr($qr): self
    {
        $this->qr = $qr;

        return $this;
    }
}
