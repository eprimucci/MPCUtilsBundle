<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use CodigoAustral\MPCUtilsBundle\Service\Parseable;


/**
 * Description of ObservationsParser
 *
 * @author piri
 */
class ObservationsParser {
    
    
    
/**
 * 
 * @see http://www.minorplanetcenter.net/iau/info/OpticalObs.html
 * 
   Columns     Format   Use
    1 -  5       A5     Minor planet number
    6 - 12       A7     Provisional or temporary designation
   13            A1     Discovery asterisk
 * 
05230         C2014 01 15.17067 10 02 34.67 -28 05 12.6          15.1 Vu~11YDX38
05230         C2014 01 15.17557 10 02 34.69 -28 05 13.3          15.1 Vu~11YDX38
05230         C2014 01 15.18171 10 02 34.72 -28 05 14.2          15.1 Vu~11YDX38
     K14E45L KC2014 03 11.12548 12 30 44.91 -13 01 18.0          16.7 Vu~11sKX38
     K14E45L KC2014 03 11.12881 12 30 46.53 -13 00 39.5          16.7 Vu~11sKX38
     K14E45L KC2014 03 11.13214 12 30 48.13 -13 00 00.4          17.3 Vu~11sKX38
     K14E48Z KC2014 03 14.11751 12 01 34.95 -04 28 38.6          18.0 Vu~12DRX38
     K14E48Z KC2014 03 14.13204 12 01 34.61 -04 27 57.0          18.4 Vu~12DRX38
     K14E48Z KC2014 03 14.14734 12 01 34.28 -04 27 13.7          18.0 Vu~12DRX38
 * 
 * 
 * * 
 * 
 * @param type $line
 */
    public function parseLine($line) {
        
        /* NUMBER
         * Columns 1-5 contain a zero-padded, right-justified number--e.g., an observation of (1) 
         * would be given as 00001, an observation of (3202) would be 03202. If there is no number these columns must 
         * be blank. Six-digit numbers are to be stored in packed form (A0000 = 100000), in order to be consistent 
         * with the format specifier earlier in this document.
         */
        $parsed['mplNumber']          =  substr($line, 0, 5);

        
        /* PROVISIONAL/TEMPORARY DESIGNATION
         * Columns 6-12 contain the provisional designation or the temporary designation. The provisional designation is 
         * stored in a 7-character packed form. Temporary designations are designations assigned by the observer for new or 
         * unidentified objects. Such designations must begin in column 6, should not exceed 6 characters in length, 
         * and should start with one or more letters.
         * It is important that every observation has a designation and that the same designation 
         * is used for all observations of the same object.
         */
        $parsed['tempDesignation']    =  substr($line, 5, 7);
        
        // DISCOVERY ASTERISK
        // Discovery observations for new (or unidentified) objects should contain `*' in column 13. Only one asterisked observation 
        // per object is expected.
        $parsed['discovery']          =  substr($line, 12, 1);

        
        // DATE OF OBSERVATIONS
        // Columns 16-32 contain the date and UTC time of the mid-point of observation. If the observation refers to one end of 
        // a trailed image, then the time of observation will be either the start time of the exposure or the finish time 
        // of the exposure. The format is "YYYY MM DD.dddddd", with the decimal day of observation normally being given to a precision 
        // of 0.00001 days. Where such precision is justified, there is the option of recording times to 0.000001 days.
        $parsed['dateObs']            =  substr($line, 15, 17);
        
        
        /* OBSERVED RA (J2000.0)
         * Columns 33-44 contain the observed J2000.0 right ascension. The format is "HH MM SS.ddd", with the 
         * seconds of R.A. normally being given to a precision of 0.01s. There is the option of recording the right 
         * ascension to 0.001s, where such precision is justified.
         */
        $parsed['RA']               =  substr($line, 32, 12);
        
        /* OBSERVED DECL (J2000.0)
         * Columns 45-56 contain the observed J2000.0 declination. The format is "sDD MM SS.dd" (with "s" being the sign), 
         * with the seconds of Decl. normally being given to a precision of 0.1". There is the option of recording 
         * the declination to 0".01, where such precision is justified.
         */
        $parsed['DEC']             =  substr($line, 44, 12);
        
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
        $parsed['mag']              = substr($line, 56,22);

        /* OBSERVATORY CODE
         * Observatory codes are stored in columns 78-80. Lists of observatory codes are published from time to time in the MPCs. 
         * Note that new observatory codes are assigned only upon receipt of acceptable astrometric observations.
         */
        $parsed['ocode']            = substr($line, 77);
        
        
        return $parsed;
        
    }


}
