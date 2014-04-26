<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Monolog\Logger;

use CodigoAustral\MPCUtilsBundle\Service\ObservationsParser;
use CodigoAustral\MPCUtilsBundle\Service\ParsedObservation;

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


    
    public function downloadLongPHAlist() {
        
        
        // TODO: verify it is not already here...
        

        
        
        // get the resource
        $bytesRead = file_put_contents($this->downloadsFolder . DIRECTORY_SEPARATOR . "longpha.html", fopen($this->mpcPHAurl, 'r'));
        if($bytesRead===false) {
            $message='Unable to download contents from: '.$this->mpcPHAurl;
            $this->logger->err($message);
            throw new \Exception($message);
        }
        
        // 
        $this->logger->info("Downloaded {$bytesRead} bytes from resource {$this->mpcPHAurl}");
        
        return array('bytes'=>$bytesRead, 'url'=>$this->mpcPHAurl);
    }

    

    
    
    public function getObservations($observatoryCode) {
        
        
        
        $targetFile=$this->downloadsFolder . DIRECTORY_SEPARATOR . "observations-{$observatoryCode}.dat";
        $url="http://www.minorplanetcenter.net/tmp/1500-01-01--2099-12-31--{$observatoryCode}.dat";
        
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
            
            $this->logger->info(var_export($types, true));
        } 
        else {
            // error opening the file.
        } 
        fclose($handle);
        
        
        
        
        
        
    }
    
    
    
    
    
    
    
    
    
    public function getDownloadsFolder() {
        return $this->downloadsFolder;
    }

    public function getMpcPHAurl() {
        return $this->mpcPHAurl;
    }


    
    
    
    
    
    
    
    
}