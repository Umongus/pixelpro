<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Periodos
 *
 * @ORM\Table(name="periodos")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PeriodosRepository")
 */
class Periodos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Trabajadores") */
    private $trabajador;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaAlta", type="date", nullable=true)
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaBaja", type="date", nullable=true)
     */
    private $fechaBaja;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entidad") */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="Comentario", type="string", length=255, nullable=true)
     */
    private $comentario;


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
     * @return Periodos
     */
    public function setTrabajador(\AppBundle\Entity\Trabajadores $trabajador)
    {
        $this->trabajador = $trabajador;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getTrabajador()
    {
        return $this->trabajador;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     *
     * @return Periodos
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     *
     * @return Periodos
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set entidad
     *
     * @param string $entidad
     *
     * @return Periodos
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
     * Set comentario
     *
     * @param string $comentario
     *
     * @return Periodos
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }
}
