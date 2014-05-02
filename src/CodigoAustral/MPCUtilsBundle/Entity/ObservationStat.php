<?php

namespace CodigoAustral\MPCUtilsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Resumen de observaciones para un periodo
 *
 * @ORM\Table(name="observation_stats")
 * @ORM\Entity
 */
class ObservationStat {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="CodigoAustral\MPCUtilsBundle\Entity\Observatory", inversedBy="observationStats", cascade={"persist"})
     * @ORM\JoinColumn(name="observatory_id", referencedColumnName="id", onDelete="SET NULL")
     * @var CodigoAustral\MPCUtilsBundle\Entity\Observatory
     */
    protected $observatory;    
    
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $startDate;


    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $endDate;
    
    /**
     * 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $data;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    private $created;

    
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

    public function setObservatory(CodigoAustral\MPCUtilsBundle\Entity\Observatory $observatory) {
        $this->observatory = $observatory;
        return $this;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
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