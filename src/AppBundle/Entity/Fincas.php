<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fincas
 *
 * @ORM\Table(name="fincas")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FincasRepository")
 */
class Fincas
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
     * @ORM\Column(name="nombre", type="string", length=50, unique=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="superficieOlivar", type="decimal", precision=10, scale=3)
     */
    private $superficieOlivar;

    /**
     * @var string
     *
     * @ORM\Column(name="superficieCalma", type="decimal", precision=10, scale=3)
     */
    private $superficieCalma;

    /**
     * @var string
     *
     * @ORM\Column(name="cultivoPrincipal", type="string", length=20)
     */
    private $cultivoPrincipal;


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
     * @return Fincas
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
     * Set superficieOlivar
     *
     * @param string $superficieOlivar
     *
     * @return Fincas
     */
    public function setSuperficieOlivar($superficieOlivar)
    {
        $this->superficieOlivar = $superficieOlivar;

        return $this;
    }

    /**
     * Get superficieOlivar
     *
     * @return string
     */
    public function getSuperficieOlivar()
    {
        return $this->superficieOlivar;
    }

    /**
     * Set superficieCalma
     *
     * @param string $superficieCalma
     *
     * @return Fincas
     */
    public function setSuperficieCalma($superficieCalma)
    {
        $this->superficieCalma = $superficieCalma;

        return $this;
    }

    /**
     * Get superficieCalma
     *
     * @return string
     */
    public function getSuperficieCalma()
    {
        return $this->superficieCalma;
    }

    /**
     * Set cultivoPrincipal
     *
     * @param string $cultivoPrincipal
     *
     * @return Fincas
     */
    public function setCultivoPrincipal($cultivoPrincipal)
    {
        $this->cultivoPrincipal = $cultivoPrincipal;

        return $this;
    }

    /**
     * Get cultivoPrincipal
     *
     * @return string
     */
    public function getCultivoPrincipal()
    {
        return $this->cultivoPrincipal;
    }

    public function __toString()
    {
      return $this->getNombre();
    }
}
