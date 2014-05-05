<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use CodigoAustral\MPCUtilsBundle\Entity\Observation;

/**
 * Description of ParsedObservation
 *
 * @author piri
 */
class ParsedObservation {

/*    
              1         2         3         4         5         6         7         8        9
012345678901234567890123456789012345678901234567890123456789012345678901234567890123456790
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| 
05230         C2014 01 15.18171 10 02 34.72 -28 05 14.2          15.1 Vu~11YDX38
     K14E45L KC2014 03 11.12548 12 30 44.91 -13 01 18.0          16.7 Vu~11sKX38 * 
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| 
         1         2         3         4         5         6         7         8        9
12345678901234567890123456789012345678901234567890123456789012345678901234567890123456790
*/
    
    protected $minorPlanetNumber;
    protected $temporaryDesignation;
    protected $discoveryAsterisk;
    protected $dateObs;
    protected $RA;
    protected $DEC;
    protected $mag;
    protected $obscode;
    
    protected $extra;
    protected $type;


    /**
     * Returns visual magnitude
     * @return boolean
     */
    public function getNumericMagnitude() {
        // mag = '          16.7 V' => 16.7
        if($this->mag=='') {
            return false;
        }
        return abs(substr($this->mag, 10, 4));
    }
    
    /**
     * Filter or band
     * @return boolean
     */
    public function getBand() {
        // mag = '          16.7 V' => 'V'
        if($this->mag=='') {
            return false;
        }
        return substr($this->mag, -1);
    }
    
    
    /**
     * Return value should match the original parsed line. 
     * Good for testing
     * @return string
     */
    public function reconstruct() {
        $discovery=($this->discoveryAsterisk?'*':' ');
        return "{$this->minorPlanetNumber}{$this->temporaryDesignation}{$discovery}{$this->dateObs}{$this->RA}{$this->DEC}{$this->mag}{$this->extra}{$this->obscode}";
    }


    
    public function getRAasFloat() {
        // 10 02 12.21
        $parts= explode(' ', $this->RA);
        return round(($parts[0]*15)+($parts[1]/4)+($parts[2]/240),6);
    }
    
    public function getDECasFloat() {
        // -27 59 17.8
        $parts= explode(' ', $this->DEC);
        $d=abs($parts[0])+($parts[1]/60)+($parts[2]/3600);
        if(substr($parts[0], 0, 1)=='-') {
            $d=$d*-1;
        }
        return round($d,6);
        
    }
    
    
    
    
    
    
    // NORMAL GETTERs AND SETTERs
    
    public function getMinorPlanetNumber() {
        return $this->minorPlanetNumber;
    }

    public function getTemporaryDesignation() {
        return $this->temporaryDesignation;
    }

    public function getDiscoveryAsterisk() {
        return $this->discoveryAsterisk;
    }

    public function getDateObs() {
        return $this->dateObs;
    }

    public function getRA() {
        return $this->RA;
    }

    public function getDEC() {
        return $this->DEC;
    }

    public function getMag() {
        return $this->mag;
    }

    public function getObscode() {
        return $this->obscode;
    }

    public function getExtra() {
        return $this->extra;
    }

    public function setMinorPlanetNumber($minorPlanetNumber) {
        $this->minorPlanetNumber = $minorPlanetNumber;
        return $this;
    }

    public function setTemporaryDesignation($temporaryDesignation) {
        $this->temporaryDesignation = $temporaryDesignation;
        return $this;
    }

    public function setDiscoveryAsterisk($discoveryAsterisk) {
        $this->discoveryAsterisk = $discoveryAsterisk;
        return $this;
    }

    public function setDateObs($dateObs) {
        $this->dateObs = $dateObs;
        return $this;
    }

    public function setRA($RA) {
        $this->RA = $RA;
        return $this;
    }

    public function setDEC($DEC) {
        $this->DEC = $DEC;
        return $this;
    }

    public function setMag($mag) {
        $this->mag = $mag;
        return $this;
    }

    public function setObscode($obscode) {
        $this->obscode = $obscode;
        return $this;
    }

    public function setExtra($extra) {
        $this->extra = $extra;
        return $this;
    }


    public function getDateTime() {
        return $this->parseUT($this->dateObs);
    }
    
    
    /**
     * Converts MPC date time format (decimal day) to PHP DateTime
     * PHP does not know that UT != UTC, anyway 0.9 secs apart is fine!
     * @param type $mpcDate
     * @return \DateTime
     */
    private function parseUT($mpcDate) {
        
        // e.g. '2014 03 11.12548' is '2014-03-11 03:00:41'
        
        $year=  substr($mpcDate, 0,4);
        $month = substr($mpcDate, 5,2);
        $dayUT=  (float)substr($mpcDate, 8);
        
        $hoursUT=fmod($dayUT, 1);
        $hours=$hoursUT*24;
        
        $minutes=fmod($hours, 1)*60;
        $seconds=fmod($minutes, 1)*60;
        
        $dateString=
                $year .'-'. 
                $month . '-'. 
                str_pad(intval($dayUT),2, '0', STR_PAD_LEFT) .' '.
                str_pad(intval($hours),2, '0', STR_PAD_LEFT).':'.
                str_pad(intval($minutes),2, '0', STR_PAD_LEFT).':'.
                str_pad(number_format($seconds,6),2, '0', STR_PAD_LEFT);
        
        return new \DateTime($dateString.' UTC');
        
    }
    


    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }


    
    
}
