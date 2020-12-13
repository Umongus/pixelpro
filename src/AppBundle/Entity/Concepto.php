<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Concepto
 *
 * @ORM\Table(name="concepto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConceptoRepository")
 */
class Concepto
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion1", type="string", length=255, nullable=true)
     */
    private $descripcion1;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion2", type="string", length=255, nullable=true)
     */
    private $descripcion2;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Concepto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion1
     *
     * @param string $descripcion1
     *
     * @return Concepto
     */
    public function setDescripcion1($descripcion1)
    {
        $this->descripcion1 = $descripcion1;

        return $this;
    }

    /**
     * Get descripcion1
     *
     * @return string
     */
    public function getDescripcion1()
    {
        return $this->descripcion1;
    }

    /**
     * Set descripcion2
     *
     * @param string $descripcion2
     *
     * @return Concepto
     */
    public function setDescripcion2($descripcion2)
    {
        $this->descripcion2 = $descripcion2;

        return $this;
    }

    /**
     * Get descripcion2
     *
     * @return string
     */
    public function getDescripcion2()
    {
        return $this->descripcion2;
    }

    public function __toString()
    {
      return $this->getNombre();
    }
}
