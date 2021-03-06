<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParteTrabajo
 *
 * @ORM\Table(name="parte_trabajo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParteTrabajoRepository")
 */
class ParteTrabajo
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Trabajadores") */
    private $trabajador;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Trabajos") */
    private $trabajo;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tipo") */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal", precision=10, scale=3)
     */
    private $cantidad;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Fincas") */
    private $finca;

    /**
     * @var int
     *
     * @ORM\Column(name="cuadrilla", type="integer")
     */
    private $cuadrilla;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Producto") */
    private $producto;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $observacion;


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
     * @param \DateTime $fecha
     *
     * @return ParteTrabajo
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set trabajador
     *
     * @param string $trabajador
     *
     * @return ParteTrabajo
     */
    public function setTrabajador(\AppBundle\Entity\Trabajadores $trabajador)
    {
        $this->trabajador = $trabajador;

        return $this;
    }

    /**
     * Get trabajador
     *
     * @return string
     */
    public function getTrabajador()
    {
        return $this->trabajador;
    }

    /**
     * Set trabajo
     *
     * @param string $trabajo
     *
     * @return ParteTrabajo
     */
    public function setTrabajo(\AppBundle\Entity\Trabajos $trabajo)
    {
        $this->trabajo = $trabajo;

        return $this;
    }

    /**
     * Get trabajo
     *
     * @return string
     */
    public function getTrabajo()
    {
        return $this->trabajo;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return ParteTrabajo
     */
    public function setTipo(\AppBundle\Entity\Tipo $tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set cantidad
     *
     * @param string $cantidad
     *
     * @return ParteTrabajo
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set finca
     *
     * @param string $finca
     *
     * @return ParteTrabajo
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
     * Set cuadrilla
     *
     * @param integer $cuadrilla
     *
     * @return ParteTrabajo
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

    /**
     * Set producto
     *
     * @param \AppBundle\Entity\Producto $producto
     *
     * @return ParteTrabajo
     */
    public function setProducto(\AppBundle\Entity\Producto $producto)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \AppBundle\Entity\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     *
     * @return ParteTrabajo
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }
}
