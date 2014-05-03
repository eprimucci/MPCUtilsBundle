<?php

namespace CodigoAustral\MPCUtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CodigoAustral\MPCUtilsBundle\Entity\Observatory;

/**
 * Resumen de observaciones para un periodo
 *
 * @ORM\Table(name="observation" , 
 *                          indexes={
 *                              @ORM\Index(name="type_idx", columns={"obs_type"}),
 *                              @ORM\Index(name="obsdate_idx", columns={"obs_datetime"})
 *                          })
 * @ORM\Entity
 */
class Observation {

    const COMET_LONG_PERIOD='CC';
    const COMET_SHORT_PERIOD='CP';
    const COMET_DEFUNCT='CD';
    const COMET_UNCERTAIN='CX';
    const COMET_NOW_MP='CA';
    const UNKNOWN='?';
    const MINOR_PLANET='MP';
    const NATSAT_JUPITER='NATSAT-J-';
    const NATSAT_SATURN='NATSAT-S-';
    const NATSAT_URANUS='NATSAT-U-';
    const NATSAT_NEPTUNE='NATSAT-N-';
    const NEO_NEW='UNEO';
    const NEO_CONFIRMED='CNEO';
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="CodigoAustral\MPCUtilsBundle\Entity\Observatory", inversedBy="observations", cascade={"persist"})
     * @ORM\JoinColumn(name="observatory_id", referencedColumnName="id", onDelete="CASCADE")
     * @var CodigoAustral\MPCUtilsBundle\Entity\Observatory
     */
    protected $observatory;    
    
    
    /**
     * 
     * @ORM\Column(name="mpnumber", type="string", length=5, nullable=true)
     * @var string
     */
    protected $minorPlanetNumber;
    
    /**
     * 
     * @ORM\Column(name="temp_designation", type="string", length=7, nullable=true)
     * @var string
     */
    protected $temporaryDesignation;
    
    /**
     * 
     * @ORM\Column(name="discovery", type="boolean")
     * @var string
     */
    protected $discovery;

    
    /**
     * 
     * @ORM\Column(name="utdate", type="string", length=18, nullable=false)
     * @var string
     */
    protected $utDate;
    
    
    /**
     * @var float
     *
     * @ORM\Column(name="ra", type="decimal", precision=9, scale=6, nullable=true)
     */    
    protected $RA;
    
    
    /**
     * @var float
     *
     * @ORM\Column(name="decli", type="decimal", precision=9, scale=6, nullable=true)
     */
    protected $DEC;

    
    /**
     * Precision 6, scale 3 = 999.999
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=3, nullable=true)
     */
    protected $mag;
    
    
    /**
     * V, C, B, i, r, etc, etc
     * @ORM\Column(name="vband", type="string", length=3, nullable=true)
     * @var string
     */
    protected $band;

    /**
     * See ParsedObservation
     * @ORM\Column(name="obs_type", type="string", length=15, nullable=false)
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(name="obs_datetime", type="datetime", nullable=false)
     * @var \DateTime
     */
    protected $dateTime;


    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $created;

    
    function __construct() {
        $this->created=new \DateTime();
    }

    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getObservatory() {
        return $this->observatory;
    }

    public function setObservatory(Observatory $observatory) {
        $this->observatory = $observatory;
        return $this;
    }

    public function getMinorPlanetNumber() {
        return $this->minorPlanetNumber;
    }

    public function setMinorPlanetNumber($minorPlanetNumber) {
        $this->minorPlanetNumber = $minorPlanetNumber;
        return $this;
    }

    public function getTemporaryDesignation() {
        return $this->temporaryDesignation;
    }

    public function setTemporaryDesignation($temporaryDesignation) {
        $this->temporaryDesignation = $temporaryDesignation;
        return $this;
    }

    public function getDiscovery() {
        return $this->discovery;
    }

    public function setDiscovery($discovery) {
        $this->discovery = $discovery;
        return $this;
    }

    public function getUtDate() {
        return $this->utDate;
    }

    public function setUtDate($utDate) {
        $this->utDate = $utDate;
        return $this;
    }

    public function getRA() {
        return $this->RA;
    }

    public function setRA($RA) {
        $this->RA = $RA;
        return $this;
    }

    public function getDEC() {
        return $this->DEC;
    }

    public function setDEC($DEC) {
        $this->DEC = $DEC;
        return $this;
    }

    public function getMag() {
        return $this->mag;
    }

    public function setMag($mag) {
        $this->mag = $mag;
        return $this;
    }

    public function getBand() {
        return $this->band;
    }

    public function setBand($band) {
        $this->band = $band;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getDateTime() {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime) {
        $this->dateTime = $dateTime;
        return $this;
    }

    
    public function getCreated() {
        return $this->created;
    }

    public function setCreated(\DateTime $created) {
        $this->created = $created;
        return $this;
    }




}