<?php

namespace CodigoAustral\MPCUtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banco
 *
 * @ORM\Table(name="catalog")
 * @ORM\Entity
 */
class Catalog {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="code", type="string", length=15, nullable=false)
     */
    private $code;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    
    public function getId() {
        return $this->id;
    }

    public function getCode() {
        return $this->code;
    }

    public function getName() {
        return $this->name;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }


    
}