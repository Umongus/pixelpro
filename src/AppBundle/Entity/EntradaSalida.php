<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntradaSalida
 *
 * @ORM\Table(name="entrada_salida")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntradaSalidaRepository")
 */
class EntradaSalida
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
     * @var \Date
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="string", length=100)
     */
    private $accion;

    /**
     * @var int
     *
     * @ORM\Column(name="peso", type="integer")
     */
    private $peso;

    /**
     * @var int
     *
     * @ORM\Column(name="cuadrilla", type="integer")
     */
    private $cuadrilla;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Variedad") */
    private $variedad;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Fincas") */
    private $finca;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Producto") */
    private $producto;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entidad") */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="lote", type="string", length=100, nullable=true)
     */
    private $lote;


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
     * Set fecha
     *
     * @param \Date $fecha
     *
     * @return EntradaSalida
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \Date
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set accion
     *
     * @param string $accion
     *
     * @return EntradaSalida
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;

        return $this;
    }

    /**
     * Get accion
     *
     * @return string
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set peso
     *
     * @param integer $peso
     *
     * @return EntradaSalida
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return int
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set variedad
     *
     * @param string $variedad
     *
     * @return EntradaSalida
     */
    public function setVariedad(\AppBundle\Entity\Variedad $variedad)
    {
        $this->variedad = $variedad;

        return $this;
    }

    /**
     * Get variedad
     *
     * @return string
     */
    public function getVariedad()
    {
        return $this->variedad;
    }

    /**
     * Set finca
     *
     * @param string $finca
     *
     * @return EntradaSalida
     */
    public function setFinca(\AppBundle\Entity\Fincas $finca)
    {
        $this->finca = $finca;

        return $this;
    }

    /**
     * Get finca
     *
     * @return string
     */
    public function getFinca()
    {
        return $this->finca;
    }

    /**
     * Set producto
     *
     * @param string $producto
     *
     * @return EntradaSalida
     */
    public function setProducto(\AppBundle\Entity\Producto $producto)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return string
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set entidad
     *
     * @param string $entidad
     *
     * @return EntradaSalida
     */
    public function setEntidad(\AppBundle\Entity\Entidad $entidad)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad
     *
     * @return string
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set lote
     *
     * @param string $lote
     *
     * @return EntradaSalida
     */
    public function setLote($lote)
    {
        $this->lote = $lote;

        return $this;
    }

    /**
     * Get lote
     *
     * @return string
     */
    public function getLote()
    {
        return $this->lote;
    }

    /**
     * Set cuadrilla
     *
     * @param integer $cuadrilla
     *
     * @return EntradaSalida
     */
    public function setCuadrilla($cuadrilla)
    {
        $this->cuadrilla = $cuadrilla;

        return $this;
    }

    /**
     * Get cuadrilla
     *
     * @return integer
     */
    public function getCuadrilla()
    {
        return $this->cuadrilla;
    }
}
