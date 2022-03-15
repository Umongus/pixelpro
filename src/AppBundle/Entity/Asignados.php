<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Asignados
 *
 * @ORM\Table(name="asignados")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AsignadosRepository")
 */
class Asignados
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
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entidad") */
    private $entidad;


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
     * Set trabajador
     *
     * @param string $trabajador
     *
     * @return Asignados
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Asignados
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
     * Set entidad
     *
     * @param string $entidad
     *
     * @return Asignados
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
}
