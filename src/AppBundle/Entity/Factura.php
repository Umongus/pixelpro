<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Factura
 *
 * @ORM\Table(name="factura")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FacturaRepository")
 */
class Factura
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entidad") */
    private $emisor;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entidad") */
    private $receptor;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="ejercicio", type="integer")
     */
    private $ejercicio;

    /**
     * @var string
     *
     * @ORM\Column(name="periodo", type="string", length=255)
     */
    private $periodo;

    /**
     * @var int
     *
     * @ORM\Column(name="numeroFactura", type="integer")
     */
    private $numeroFactura;

    /**
     * @var string
     *
     * @ORM\Column(name="declarada", type="string", length=255)
     */
    private $declarada;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Producto") */
    private $producto;


    /**
     * @var string
     *
     * @ORM\Column(name="retencion", type="string", length=255)
     */
    private $retencion;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje", type="decimal", precision=4, scale=3)
     */
    private $porcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="base", type="decimal", precision=10, scale=4)
     */
    private $base;

    /**
     * @var string
     *
     * @ORM\Column(name="iva", type="decimal", precision=10, scale=4)
     */
    private $iva;

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
     * Set producto
     *
     * @param \AppBundle\Entity\Producto $producto
     *
     * @return Factura
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
     * Set emisor
     *
     * @param string $emisor
     *
     * @return Factura
     */
    public function setEmisor(\AppBundle\Entity\Entidad $emisor)
    {
        $this->emisor = $emisor;

        return $this;
    }

    /**
     * Get emisor
     *
     * @return string
     */
    public function getEmisor()
    {
        return $this->emisor;
    }

    /**
     * Set receptor
     *
     * @param string $receptor
     *
     * @return Factura
     */
    public function setReceptor(\AppBundle\Entity\Entidad $receptor)
    {
        $this->receptor = $receptor;

        return $this;
    }

    /**
     * Get receptor
     *
     * @return string
     */
    public function getReceptor()
    {
        return $this->receptor;
    }

    /**
     * Set fecha
     *
     * @param \Date $fecha
     *
     * @return Factura
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
     * Set declarada
     *
     * @param string $declarada
     *
     * @return Factura
     */
    public function setDeclarada($declarada)
    {
        $this->declarada = $declarada;

        return $this;
    }

    /**
     * Get declarada
     *
     * @return string
     */
    public function getDeclarada()
    {
        return $this->declarada;
    }

    /**
     * Set tipoRetencion
     *
     * @param string $tipoRetencion
     *
     * @return Factura
     */
    public function setTipoRetencion($tipoRetencion)
    {
        $this->tipoRetencion = $tipoRetencion;

        return $this;
    }

    /**
     * Get tipoRetencion
     *
     * @return string
     */
    public function getTipoRetencion()
    {
        return $this->tipoRetencion;
    }

    /**
     * Set retencion
     *
     * @param string $retencion
     *
     * @return Factura
     */
    public function setRetencion($retencion)
    {
        $this->retencion = $retencion;

        return $this;
    }

    /**
     * Get retencion
     *
     * @return string
     */
    public function getRetencion()
    {
        return $this->retencion;
    }

    /**
     * Set ejercicio
     *
     * @param integer $ejercicio
     *
     * @return Factura
     */
    public function setEjercicio($ejercicio)
    {
        $this->ejercicio = $ejercicio;

        return $this;
    }

    /**
     * Get ejercicio
     *
     * @return integer
     */
    public function getEjercicio()
    {
        return $this->ejercicio;
    }

    /**
     * Set periodo
     *
     * @param string $periodo
     *
     * @return Factura
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * Set numeroFactura
     *
     * @param integer $numeroFactura
     *
     * @return Factura
     */
    public function setNumeroFactura($numeroFactura)
    {
        $this->numeroFactura = $numeroFactura;

        return $this;
    }

    /**
     * Get numeroFactura
     *
     * @return integer
     */
    public function getNumeroFactura()
    {
        return $this->numeroFactura;
    }

    /**
     * Set porcentaje
     *
     * @param string $porcentaje
     *
     * @return Factura
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return string
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set base
     *
     * @param string $base
     *
     * @return Factura
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get base
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set iva
     *
     * @param string $iva
     *
     * @return Factura
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return string
     */
    public function getIva()
    {
        return $this->iva;
    }
}
