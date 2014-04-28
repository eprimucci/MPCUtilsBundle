<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Monolog\Logger;

use CodigoAustral\MPCUtilsBundle\Service\ObservationsParser;
use CodigoAustral\MPCUtilsBundle\Service\ParsedObservation;
use CodigoAustral\MPCUtilsBundle\Service\ObservatoryParser;

class MpcResourcesService {

    /**
     *
     * @var ObjectManager
     */
    protected $om;
    
    /**
     *
     * @var Logger
     */
    protected $logger;
    
    
    protected $downloadsFolder;
    
    
    protected $mpcPHAurl;
    

    function __construct(ObjectManager $om, Logger $logger, $downloadsFolder, $mpcPHAurl) {
        $this->om = $om;
        $this->logger = $logger;
        $this->downloadsFolder = $downloadsFolder;
        $this->mpcPHAurl = $mpcPHAurl;
    }


    
    
    /**
     * Downloads complete set of observations for the given Observatory code unless
     * we have a local copy. Parses data and performs simple stats.
     * Some observatories could observe thousands or millions that is why I only return stats now.
     * @param type $observatoryCode
     * @return type Array
     * @throws \Exception
     */
    public function getObservations($observatoryCode) {
        
        $targetFile=$this->downloadsFolder . DIRECTORY_SEPARATOR . "observations-{$observatoryCode}.dat";
        
        // this url will not be available until we query via web form (it is created on the fly...)
        $url="http://www.minorplanetcenter.net/tmp/1500-01-01--2099-12-31--{$observatoryCode}.dat";
        
        // prime it...
        @fopen("http://www.minorplanetcenter.net/db_search/show_by_date?utf8=%E2%9C%93&start_date=&end_date=&observatory_code={$observatoryCode}&obj_type=all", 'r');
        
        // now hit the url
        if(file_exists($targetFile)) {
            $bytesRead = filesize($targetFile);
            $this->logger->info("Read {$bytesRead} bytes from local resource {$targetFile}");
        }
        else {
            $bytesRead = file_put_contents($targetFile, fopen($url, 'r'));
            if($bytesRead===false) {
                $message='Unable to download contents from: '.$this->mpcPHAurl;
                $this->logger->err($message);
                throw new \Exception($message);
            }
            $this->logger->info("Downloaded {$bytesRead} bytes from resource {$this->mpcPHAurl}");
        }

        // now parse it according to MPC instructions:
        $handle = fopen($targetFile, "r");
        if ($handle) {
            $parser=new ObservationsParser();
            
            $types=array('total'=>0);
            
            while (($line = fgets($handle)) !== false) {
                /* @var $p ParsedObservation */
                $p=$parser->parseLine($line);
                
                $type=$p->getType();
                
                if(array_key_exists($type, $types)) {
                    $types[$type]=$types[$type]+1;
                }
                else {
                    $types[$type]=1;
                }
                $types['total']++;
                
                $this->logger->info("TYPE:--{$p->getType()}--{$p->reconstruct()}--");
            }
        } 
        else {
            // error opening the file.
        } 
        fclose($handle);
        
        return $types;
    }
    
    
    
    /**
     * Downloads latest observatories file from MPC unless we have a local copy.
     * Parses every line returning an array.
     * Lon, Cos and Sin and returned as floats. Null for spaceborn scopes.
     * @return type
     * @throws \Exception
     */
    public function getObservatories() {
        
        $targetFile=$this->downloadsFolder . DIRECTORY_SEPARATOR . "observatories.txt";
        
        $url='http://www.minorplanetcenter.net/iau/lists/ObsCodes.html';

        if(file_exists($targetFile)) {
            $bytesRead = filesize($targetFile);
            $this->logger->info("Read {$bytesRead} bytes from local resource {$targetFile}");
        }
        else {
            $bytesRead = file_put_contents($targetFile, fopen($url, 'r'));
            if($bytesRead===false) {
                $message='Unable to download contents from: '.$url;
                $this->logger->err($message);
                throw new \Exception($message);
            }
            $this->logger->info("Downloaded {$bytesRead} bytes from resource {$url}");
        }

        // now parse it according to MPC instructions:
        $handle = fopen($targetFile, "r");
        if ($handle) {
            $parser=new ObservatoryParser();
            
            $obs=array();
            
            while (($line = fgets($handle)) !== false) {
                /* @var $p ParsedObservation */
                if(substr($line, 0,1)=='<' || substr($line, 0,4)=='Code') {
                    continue;
                }
                
                $obs[]=$parser->parseLine($line);

            }
            $this->logger->info('Parsed '.count($obs).' records.');
        } 
        else {
            // error opening the file.
        } 
        fclose($handle);
        return $obs;
    }
    
    
    
    public function getDownloadsFolder() {
        return $this->downloadsFolder;
    }

    public function getMpcPHAurl() {
        return $this->mpcPHAurl;
    }


    
    
    
    
    
    
    
    
}