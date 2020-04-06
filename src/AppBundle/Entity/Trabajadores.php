<?php
namespace AppBundle\Entity;
//src/AppBundle/Entity/Trabajadores.php
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="trabajadores")
 */
class Trabajadores
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3)
     */
    private $nombre;


    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $observaciones;

    /**
     * Get id
     *
     * @return integer
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
     * @return Trabajadores
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
     * Set dni
     *
     * @param string $dni
     *
     * @return Trabajadores
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Trabajadores
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    public function __toString()
    {
      return $this->getNombre();
    }
}
