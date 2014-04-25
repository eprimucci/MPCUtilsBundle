<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

use Monolog\Logger;

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

    

    
    
    
    
    
    
    
    
    
    
    
    
    public function getDownloadsFolder() {
        return $this->downloadsFolder;
    }

    public function getMpcPHAurl() {
        return $this->mpcPHAurl;
    }


    
    
    
    
    
}