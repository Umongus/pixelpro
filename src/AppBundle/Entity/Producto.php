<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductoRepository")
 */
class Producto
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
     * @ORM\Column(name="nombre", type="string", length=15, unique=true)
     */
    private $nombre;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicioCampo", type="datetime")
     */
    private $fechaInicioCampo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFinCampo", type="datetime")
     */
    private $fechaFinCampo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicioAlmacen", type="datetime")
     */
    private $fechaInicioAlmacen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFinAlmacen", type="datetime")
     */
    private $fechaFinAlmacen;


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
     * @return Producto
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
     * Set year
     *
     * @param integer $year
     *
     * @return Producto
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    public function __toString()
    {
      return $this->getNombre();
    }

    /**
     * Set fechaInicioCampo
     *
     * @param \DateTime $fechaInicioCampo
     *
     * @return Producto
     */
    public function setFechaInicioCampo($fechaInicioCampo)
    {
        $this->fechaInicioCampo = $fechaInicioCampo;

        return $this;
    }

    /**
     * Get fechaInicioCampo
     *
     * @return \DateTime
     */
    public function getFechaInicioCampo()
    {
        return $this->fechaInicioCampo;
    }

    /**
     * Set fechaFinCampo
     *
     * @param \DateTime $fechaFinCampo
     *
     * @return Producto
     */
    public function setFechaFinCampo($fechaFinCampo)
    {
        $this->fechaFinCampo = $fechaFinCampo;

        return $this;
    }

    /**
     * Get fechaFinCampo
     *
     * @return \DateTime
     */
    public function getFechaFinCampo()
    {
        return $this->fechaFinCampo;
    }

    /**
     * Set fechaInicioAlmacen
     *
     * @param \DateTime $fechaInicioAlmacen
     *
     * @return Producto
     */
    public function setFechaInicioAlmacen($fechaInicioAlmacen)
    {
        $this->fechaInicioAlmacen = $fechaInicioAlmacen;

        return $this;
    }

    /**
     * Get fechaInicioAlmacen
     *
     * @return \DateTime
     */
    public function getFechaInicioAlmacen()
    {
        return $this->fechaInicioAlmacen;
    }

    /**
     * Set fechaFinAlmacen
     *
     * @param \DateTime $fechaFinAlmacen
     *
     * @return Producto
     */
    public function setFechaFinAlmacen($fechaFinAlmacen)
    {
        $this->fechaFinAlmacen = $fechaFinAlmacen;

        return $this;
    }

    /**
     * Get fechaFinAlmacen
     *
     * @return \DateTime
     */
    public function getFechaFinAlmacen()
    {
        return $this->fechaFinAlmacen;
    }
}
