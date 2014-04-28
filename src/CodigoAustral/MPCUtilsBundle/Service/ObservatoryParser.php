<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use CodigoAustral\MPCUtilsBundle\Service\Parseable;
use CodigoAustral\MPCUtilsBundle\Service\ParsedObservation;

/**
 * Description of ObservationsParser
 *
 * @author piri
 */
class ObservatoryParser implements Parseable {

    
    
    public function parseDocument($document) {
        throw new \Exception('Not implemented');
    }

    public function parseLine($line) {

        /**

          1         2         3         4         5         6         7         8        9
012345678901234567890123456789012345678901234567890123456789012345678901234567890123456790
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| 
Code  Long.   cos      sin    Name
J59 356.202560.726956+0.684386Observatorio Linceo, Santander
000   0.0000 0.62411 +0.77873 Greenwich
C49                           STEREO-A
C50                           STEREO-B
C51                           WISE
001   0.1542 0.62992 +0.77411 Crowborough
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| 
         1         2         3         4         5         6         7         8        9
12345678901234567890123456789012345678901234567890123456789012345678901234567890123456790
        */
        
        $code=(substr($line, 0, 3));
        $long=(substr($line, 4, 9));
        $cos=(substr($line, 13, 8));
        $sin=(substr($line, 21, 9));
        
        if(trim($long)=='') {
            $long='null';
        }
        else {
            $long=  floatval($long);
        }

        if(trim($cos)=='') {
            $cos='null';
        }
        else {
            $cos=  floatval($cos);
        }

        
        if(trim($sin)=='') {
            $sin='null';
        }
        else {
            $sin=  floatval($sin);
        }

        
        return array(
            'code'=>$code,
            'long'=>$long,
            'cos'=>$cos,
            'sin'=>$sin,
            'name'=>(substr($line, 30)),
        );
        
        
    }

}
