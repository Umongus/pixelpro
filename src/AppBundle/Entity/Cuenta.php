<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cuenta
 *
 * @ORM\Table(name="cuenta")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CuentaRepository")
 */
class Cuenta
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
     * @ORM\Column(name="numeracion", type="string", length=255, unique=true)
     */
    private $numeracion;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entidad") */
    private $propietario;

    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entidad") */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="clase", type="string", length=255)
     */
    private $clase;


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
     * Set numeracion
     *
     * @param string $numeracion
     *
     * @return Cuenta
     */
    public function setNumeracion($numeracion)
    {
        $this->numeracion = $numeracion;

        return $this;
    }

    /**
     * Get numeracion
     *
     * @return string
     */
    public function getNumeracion()
    {
        return $this->numeracion;
    }

    /**
     * Set propietario
     *
     * @param string $propietario
     *
     * @return Cuenta
     */
    public function setPropietario(\AppBundle\Entity\Entidad $propietario)
    {
        $this->propietario = $propietario;

        return $this;
    }

    /**
     * Get propietario
     *
     * @return string
     */
    public function getPropietario()
    {
        return $this->propietario;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Cuenta
     */
    public function setUsuario(\AppBundle\Entity\Entidad $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set clase
     *
     * @param string $clase
     *
     * @return Cuenta
     */
    public function setClase($clase)
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * Get clase
     *
     * @return string
     */
    public function getClase()
    {
        return $this->clase;
    }

    public function __toString()
    {
      return $this->getNumeracion();
    }
}
