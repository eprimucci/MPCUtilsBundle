<?php

namespace CodigoAustral\MPCUtilsBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class Usuario extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="apellido", type="string", length=255, nullable=true)
     */
    protected $apellido;
    
    /**
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    protected $nombre;
    
            

    public function __construct() {
        parent::__construct();
        // your own logic
    }
    
    
    public function getApellido() {
        return $this->apellido;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }



}