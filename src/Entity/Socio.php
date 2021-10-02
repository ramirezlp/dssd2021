<?php

namespace App\Entity;

use App\Repository\SocioRepository;
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
     * @ORM\OneToMany(targetEntity=SociedadAnonimaSocio::class, mappedBy="socio")
     */
    private $sociedadesAnonimas;
    

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
}
