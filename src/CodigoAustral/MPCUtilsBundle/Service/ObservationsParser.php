<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use CodigoAustral\MPCUtilsBundle\Service\Parseable;
use CodigoAustral\MPCUtilsBundle\Service\ParsedObservation;
use CodigoAustral\MPCUtilsBundle\Entity\Observation;

/**
 * Description of ObservationsParser
 *
 * @author piri
 */
class ObservationsParser implements Parseable {

    
    
/**
 * 
 * @see http://www.minorplanetcenter.net/iau/info/OpticalObs.html
 * 
 * 
          1         2         3         4         5         6         7         8        9
012345678901234567890123456789012345678901234567890123456789012345678901234567890123456790
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| 
05230         C2014 01 15.18171 10 02 34.72 -28 05 14.2          15.1 Vu~11YDX38
     K14E45L KC2014 03 11.12548 12 30 44.91 -13 01 18.0          16.7 Vu~11sKX38 * 
    PK09S020  C2009 09 24.16115 03 33 40.39 -25 14 03.3                r67115I21
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| 
         1         2         3         4         5         6         7         8        9
12345678901234567890123456789012345678901234567890123456789012345678901234567890123456790
*/
  
    /**
     * 
     * @param type $line
     * @return \CodigoAustral\MPCUtilsBundle\Service\ParsedObservation
     */
    public function parseLine($line) {

        
        $p = new ParsedObservation();
        $type=  Observation::UNKNOWN;
        
        $p->setMinorPlanetNumber(substr($line, 0, 5));

        if(in_array(substr($p->getMinorPlanetNumber(),-1), array('C','P','D','X','A'))) {
            /* COMET
            ORBIT TYPE, col 5!
            Column 5 contains `C' for a long-period comet, `P' for a short-period comet, `D' for a `defunct' comet, `X' 
            for an uncertain comet or `A' for a minor planet given a cometary designation.
               Columns     Format   Use
                1 -  4       I4     Periodic comet number
                5            A1     Letter indicating type of orbit
                6 - 12       A7     Provisional or temporary designation
               13            X      Not used, must be blank
             * 
             */
            switch(substr($p->getMinorPlanetNumber(),-1)) {
                case 'C'; $type=Observation::COMET_LONG_PERIOD; break;
                case 'P'; $type=Observation::COMET_SHORT_PERIOD; break;
                case 'D'; $type=Observation::COMET_DEFUNCT; break;
                case 'X'; $type=Observation::COMET_UNCERTAIN; break;
                case 'A'; $type=Observation::COMET_NOW_MP; break;
            }
        }
        else {
            // MP or NATSAT
            if(in_array(substr($p->getMinorPlanetNumber(),0,1), array('J','S','U','N'))) {
                /**
                 * NATURAL SATELLITES
                 *    Columns     Format   Use
                        1            A1     Planet identifier
                        2 -  4       I3     Satellite number
                        5            A1     "S"
                        6 - 12       A7     Provisional or temporary designation
                       13            X      Not used, must be blank
                 */
                switch(substr($p->getMinorPlanetNumber(),0,1)) {
                    case 'J'; $type=Observation::NATSAT_JUPITER.  substr($p->getMinorPlanetNumber(), 1,3); break;
                    case 'S'; $type=Observation::NATSAT_SATURN.  substr($p->getMinorPlanetNumber(), 1,3); break;
                    case 'U'; $type=Observation::NATSAT_URANUS.  substr($p->getMinorPlanetNumber(), 1,3); break;
                    case 'N'; $type=Observation::NATSAT_NEPTUNE.  substr($p->getMinorPlanetNumber(), 1,3); break;
                }
                /**
                PROVISIONAL DESIGNATION
                Columns 6-12 contain a packed version of the provisional designation. The first two digits of the year are packed into a 
                 * single character in column 6 (I = 18, J = 19, K = 20). Columns 7-8 contain the last two 
                 * digits of the year. Column 9 contains the half-month letter. Columns 10-11 contain the order within the 
                 * half-month. Column 12 will be normally be `0', except for split comets, when the fragment designation is stored there as 
                 * a lower-case letter.                
                 */
                
            }
            else {
                /**
                 *    Columns     Format   Use
                        1 -  5       A5     Minor planet number
                        6 - 12       A7     Provisional or temporary designation
                       13            A1     Discovery asterisk
                 */
                $type=  Observation::MINOR_PLANET;
            }
        }

        /* PROVISIONAL/TEMPORARY DESIGNATION
         * Columns 6-12 contain the provisional designation or the temporary designation. The provisional designation is 
         * stored in a 7-character packed form. Temporary designations are designations assigned by the observer for new or 
         * unidentified objects. Such designations must begin in column 6, should not exceed 6 characters in length, 
         * and should start with one or more letters.
         * It is important that every observation has a designation and that the same designation 
         * is used for all observations of the same object.
         */
        $p->setTemporaryDesignation(substr($line, 5, 7));
        
        
        // DISCOVERY ASTERISK
        // Discovery observations for new (or unidentified) objects should contain `*' in column 13. Only one asterisked observation 
        // per object is expected.
        $p->setDiscoveryAsterisk(
                (substr($line, 12, 1)=='*'?true:false)
                );

        
        // DATE OF OBSERVATIONS
        // Columns 16-32 contain the date and UTC time of the mid-point of observation. If the observation refers to one end of 
        // a trailed image, then the time of observation will be either the start time of the exposure or the finish time 
        // of the exposure. The format is "YYYY MM DD.dddddd", with the decimal day of observation normally being given to a precision 
        // of 0.00001 days. Where such precision is justified, there is the option of recording times to 0.000001 days.
        $p->setDateObs(substr($line, 15, 17));
        
        
        /* OBSERVED RA (J2000.0)
         * Columns 33-44 contain the observed J2000.0 right ascension. The format is "HH MM SS.ddd", with the 
         * seconds of R.A. normally being given to a precision of 0.01s. There is the option of recording the right 
         * ascension to 0.001s, where such precision is justified.
         */
        $p->setRA(substr($line, 32, 12));
        
        /* OBSERVED DECL (J2000.0)
         * Columns 45-56 contain the observed J2000.0 declination. The format is "sDD MM SS.dd" (with "s" being the sign), 
         * with the seconds of Decl. normally being given to a precision of 0.1". There is the option of recording 
         * the declination to 0".01, where such precision is justified.
         */
        $p->setDEC(substr($line, 44, 12));
        
        /* OBSERVED MAGNITUDE AND BAND
         * The observed magnitude (normally to a precision of 0.1 mag.) and the band in which the measurement was made. The 
         * observed magnitude can be given to 0.01 mag., where such precision is justified. The default magnitude scale is photographic, 
         * although magnitudes may be given in V- or R-band, for example. For comets, the magnitude must be specified as 
         * being nuclear, N, or total, T. The current list of acceptable magnitude bands 
         * is: B (default if band is not indicated), V, R, I, J, W, U, g, r, i, w, y and z. 
         * Non-recognized magnitude bands will cause observations to be rejected. Addition of new recognised bands requires 
         * knowledge of a standard correction to convert a magnitude in that band to V. The formerly-used "C" band to 
         * indicate "clear" or "no filter" is no longer valid for newly-submitted observations, but will remain 
         * on previously-submitted observations.
         */
        $p->setMag(substr($line, 55,16));

        /* OBSERVATORY CODE
         * Observatory codes are stored in columns 78-80. Lists of observatory codes are published from time to time in the MPCs. 
         * Note that new observatory codes are assigned only upon receipt of acceptable astrometric observations.
         */
        $p->setObscode(substr($line, 77,3));
        
        
        // other stuff i have no clue what is it...
        $p->setExtra(substr($line, 71,6));
        
        // rules to identify object type
        
        $p->setType($type);
        
        return $p;
        
    }

    public function parseDocument($document) {
        throw new \Exception('not implemented');
   }

}
