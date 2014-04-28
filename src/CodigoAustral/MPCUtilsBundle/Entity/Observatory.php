<?php

namespace CodigoAustral\MPCUtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cuenta
 *
 * @ORM\Table(name="observatory")
 * @ORM\Entity
 */
class Observatory {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=3, nullable=false)
     */
    private $code;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="decimal", precision=10, scale=6, nullable=false)
     */
    private $longitude;

    /**
     * @var float
     *
     * @ORM\Column(name="cosine", type="decimal", precision=10, scale=6, nullable=false)
     */
    private $cosine;

    /**
     * @var float
     *
     * @ORM\Column(name="sine", type="decimal", precision=10, scale=6, nullable=false)
     */
    private $sine;

    
    public function getId() {
        return $this->id;
    }

    public function getCode() {
        return $this->code;
    }

    public function getName() {
        return $this->name;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function getCosine() {
        return $this->cosine;
    }

    public function getSine() {
        return $this->sine;
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

    public function setLongitude($longitude) {
        $this->longitude = $longitude;
        return $this;
    }

    public function setCosine($cosine) {
        $this->cosine = $cosine;
        return $this;
    }

    public function setSine($sine) {
        $this->sine = $sine;
        return $this;
    }


    
    

}