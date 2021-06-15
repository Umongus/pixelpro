<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LineaFactura
 *
 * @ORM\Table(name="linea_factura")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LineaFacturaRepository")
 */
class LineaFactura
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Factura") */
    private $factura;

    /**
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal", precision=10, scale=4)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="precio", type="decimal", precision=10, scale=4)
     */
    private $precio;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Concepto") */
    private $concepto;

    /**
     * @var string
     *
     * @ORM\Column(name="iva", type="decimal", precision=4, scale=3)
     */
    private $iva;

    /**
     * @var string
     *
     * @ORM\Column(name="variable", type="string", length=255, nullable=true)
     */
    private $variable;

    /**
     * @var string
     *
     * @ORM\Column(name="destino", type="string", length=255, nullable=true)
     */
    private $destino;


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
     * Set factura
     *
     * @param string $factura
     *
     * @return LineaFactura
     */
    public function setFactura(\AppBundle\Entity\Factura $factura)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return string
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * Set cantidad
     *
     * @param string $cantidad
     *
     * @return LineaFactura
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
     * Set precio
     *
     * @param string $precio
     *
     * @return LineaFactura
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return string
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set concepto
     *
     * @param string $concepto
     *
     * @return LineaFactura
     */
    public function setConcepto(\AppBundle\Entity\Concepto $concepto)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return string
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * Set iva
     *
     * @param string $iva
     *
     * @return LineaFactura
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

    /**
     * Set variable
     *
     * @param string $variable
     *
     * @return LineaFactura
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * Get variable
     *
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * Set destino
     *
     * @param string $destino
     *
     * @return LineaFactura
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;

        return $this;
    }

    /**
     * Get destino
     *
     * @return string
     */
    public function getDestino()
    {
        return $this->destino;
    }
}
