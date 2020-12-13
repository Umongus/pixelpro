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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="numero1", type="integer")
     */
    private $numero1;

    /**
     * @var int
     *
     * @ORM\Column(name="numero2", type="integer")
     */
    private $numero2;

    /**
     * @var string
     *
     * @ORM\Column(name="declarada", type="string", length=255)
     */
    private $declarada;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoRetencion", type="string", length=255)
     */
    private $tipoRetencion;

    /**
     * @var string
     *
     * @ORM\Column(name="retencion", type="decimal", precision=4, scale=3)
     */
    private $retencion;


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
    public function setReceptor($receptor)
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
     * @param \DateTime $fecha
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
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set numero1
     *
     * @param integer $numero1
     *
     * @return Factura
     */
    public function setNumero1($numero1)
    {
        $this->numero1 = $numero1;

        return $this;
    }

    /**
     * Get numero1
     *
     * @return int
     */
    public function getNumero1()
    {
        return $this->numero1;
    }

    /**
     * Set numero2
     *
     * @param integer $numero2
     *
     * @return Factura
     */
    public function setNumero2($numero2)
    {
        $this->numero2 = $numero2;

        return $this;
    }

    /**
     * Get numero2
     *
     * @return int
     */
    public function getNumero2()
    {
        return $this->numero2;
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
}
