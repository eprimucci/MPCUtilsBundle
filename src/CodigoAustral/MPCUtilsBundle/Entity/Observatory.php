<?php

namespace CodigoAustral\MPCUtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(name="longitude", type="decimal", precision=10, scale=6, nullable=true)
     */
    private $longitude;

    /**
     * @var float
     *
     * @ORM\Column(name="cosval", type="decimal", precision=10, scale=6, nullable=true)
     */
    private $cos;

    /**
     * @var float
     *
     * @ORM\Column(name="sinval", type="decimal", precision=10, scale=6, nullable=true)
     */
    private $sin;

    
    /**
     * @ORM\OneToMany(targetEntity="CodigoAustral\MPCUtilsBundle\Entity\Observation", mappedBy="observatory", cascade={"persist"})
     * @var ArrayCollection
     */
    private $observations;
    
    
    /**
     * @ORM\Column(name="obs_last_download", type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $lastObsDownload;
    
    
    /**
     * @ORM\Column(name="download_priority", type="integer", nullable=false)
     */
    protected $downloadPriority;
    
    
    
    
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

    public function getCos() {
        return $this->cos;
    }

    public function getSin() {
        return $this->sin;
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

    public function setCos($cos) {
        $this->cos = $cos;
        return $this;
    }

    public function setSin($sin) {
        $this->sin = $sin;
        return $this;
    }


    public function __toString() {
        return $this->code.'-'.$this->name;
    }

 
    public function getObservations() {
        return $this->observations;
    }

    public function setObservations(ArrayCollection $observations) {
        $this->observations = $observations;
        return $this;
    }
    
    public function addObservation(Observation $observation) {
        $this->observations->add($observation);
    }


    

}