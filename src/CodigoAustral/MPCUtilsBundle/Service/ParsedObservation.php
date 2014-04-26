<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

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


    public function getType() {
        $type='?';
        
        // numbered minor planet?
        if(is_numeric($this->minorPlanetNumber)) {
            return 'MP';
        }
        
        // comet?
        //ORBIT TYPE, col 5!
        // Column 5 contains `C' for a long-period comet, `P' for a short-period comet, `D' for a `defunct' comet, `X' 
        // for an uncertain comet or `A' for a minor planet given a cometary designation.
        switch($this->minorPlanetNumber) {
            case 'C'; $type='CC'; break;
            case 'P'; $type='CP'; break;
            case 'D'; $type='CD'; break;
            case 'X'; $type='CX'; break;
            case 'A'; $type='CA'; break;
        // what else could it be?
        /*    Columns     Format   Use
                1            A1     Planet identifier
                2 -  4       I3     Satellite number
                5            A1     "S"
                6 - 12       A7     Provisional or temporary designation
               13            X      Not used, must be blank
         * 
         */
            case 'J'; $type='NATSAT-J-'.  substr($this->minorPlanetNumber, 1,3); break;
            case 'S'; $type='NATSAT-S-'.  substr($this->minorPlanetNumber, 1,3); break;
            case 'U'; $type='NATSAT-U-'.  substr($this->minorPlanetNumber, 1,3); break;
            case 'N'; $type='NATSAT-N-'.  substr($this->minorPlanetNumber, 1,3); break;
            default: $type='?';
        }
        if($type!='?') {
            return $type;
        }
        
        // neo?
        if($this->minorPlanetNumber=='     ' && $this->temporaryDesignation!='') {
            $type='UNEO';
        }
        
        
        
        
        
        return $type;
    }
    
    
    
    
    public function getNumericMagnitude() {
        // mag = '          16.7 V' => 16.7
        return abs(substr($this->mag, 10, 4));
    }
    
    public function getBand() {
        // mag = '          16.7 V' => 'V'
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



    
    
    
}
