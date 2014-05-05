<?php

namespace CodigoAustral\MPCUtilsBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Monolog\Logger;

use CodigoAustral\MPCUtilsBundle\Service\ObservationsParser;
use CodigoAustral\MPCUtilsBundle\Service\ParsedObservation;
use CodigoAustral\MPCUtilsBundle\Service\ObservatoryParser;
use CodigoAustral\MPCUtilsBundle\Entity\Observatory;
use CodigoAustral\MPCUtilsBundle\Entity\Observation;

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
     * Downloads observations for the given Observatory code unless
     * we have a local copy. Parses data and stores the data for later analysis
     *
     * @param \CodigoAustral\MPCUtilsBundle\Entity\Observatory $observatory
     * @param type $overwrite
     * @param type $startDate
     * @param type $endDate
     * @return type
     * @throws \Exception
     */
    public function getObservations(Observatory $observatory, $overwrite=false, $startDate='1500-01-01', $endDate='2099-12-31') {
        
        if($observatory==null) {
            throw new \Exception('NULL observatory?');
        }
        
        $targetFile=$this->buildObservationsLocalFilename($observatory, $startDate, $endDate);
        
        if(file_exists($targetFile) && !$overwrite) {
            $bytesRead = filesize($targetFile);
            if($bytesRead==0) {
                $this->logger->warn("Zero byte local data file: {$targetFile}");
                return;
            }
            $this->logger->info("Observations file is {$bytesRead} long at {$targetFile}");
        }
        else {
            // this url will not be available until we query via web form (it is created on the fly...)
            $url=$this->getObservationsURL($observatory, $startDate, $endDate);
            
            // prime it first...
            @fopen($this->buildObservationsURLPrimer($observatory, $startDate, $endDate), 'r');
            
            $remoteFileHandle=@fopen($url, 'r');
            
            if($remoteFileHandle===false) {
                $this->logger->warn("No online data form {$observatory->getCode()} dates {$startDate}-{$endDate}");
                return;
            }
            $bytesRead = file_put_contents($targetFile, $remoteFileHandle);
            if($bytesRead===false) {
                $message='Unable to download contents from: '.$url;
                $this->logger->err($message);
                throw new \Exception($message);
            }
            $this->logger->info("Downloaded {$bytesRead} bytes from resource {$url}");
            @fclose($remoteFileHandle);
        }
        return $targetFile;
    }
    
    
    /**
     * File naming convention for downloaded observations
     * 
     * @param \CodigoAustral\MPCUtilsBundle\Entity\Observatory $observatory
     * @param type $startDate
     * @param type $endDate
     * @return type
     */
    public function buildObservationsLocalFilename(Observatory $observatory, $startDate, $endDate) {
        return $this->downloadsFolder . DIRECTORY_SEPARATOR . "observations-{$observatory->getCode()}--{$startDate}--{$endDate}.dat";
    }
    
    
    /**
     * Get URL to trigger observations file availability on MPC website
     * @param \CodigoAustral\MPCUtilsBundle\Entity\Observatory $observatory
     * @param type $startDate
     * @param type $endDate
     * @return type
     */
    public function buildObservationsURLPrimer(Observatory $observatory, $startDate, $endDate) {
        return 'http://www.minorplanetcenter.net/db_search/show_by_date?utf8=%E2%9C%93&start_date='
        .$startDate
        .'&end_date='
        .$endDate
        .'&observatory_code='
        .$observatory->getCode()
        .'&obj_type=all';
    }
    
    /**
     * Temporary filename on MPC website available for download after it has been queried
     * 
     * @param \CodigoAustral\MPCUtilsBundle\Entity\Observatory $observatory
     * @param type $startDate
     * @param type $endDate
     * @return type
     */
    public function getObservationsURL(Observatory $observatory, $startDate, $endDate) {
        return "http://www.minorplanetcenter.net/tmp/{$startDate}--{$endDate}--{$observatory->getCode()}.dat";
    }
    
    
    
    public function parseAndLoadRawObservationsFile(Observatory $observatory, $targetFile) {
        // now parse it according to MPC instructions:
        $handle = @fopen($targetFile, "r");
        if($handle===false) {
            $this->logger->warn("Unable to open {$targetFile}");
            return false;
        }
        
        $parser=new ObservationsParser();

        $obs=0;
        while (($line = fgets($handle)) !== false) {
            /* @var $p ParsedObservation */
            $p=$parser->parseLine($line);
            
            $os=new Observation();
            $os->setObservatory($observatory);
            $os->setBand($p->getBand());
            $os->setDEC($p->getDECasFloat());
            $os->setDateTime($p->getDateTime());
            $os->setDiscovery($p->getDiscoveryAsterisk());
            $os->setMag($p->getNumericMagnitude());
            $os->setMinorPlanetNumber($p->getMinorPlanetNumber());
            $os->setRA($p->getRAasFloat());
            $os->setTemporaryDesignation($p->getTemporaryDesignation());
            $os->setType($p->getType());
            $os->setUtDate($p->getDateObs());
            $this->om->persist($os);
            $obs++;
            
        }
        fclose($handle);
        
        $this->om->flush();
        $this->logger->info("Loaded {$obs} observations for Observatory {$observatory->getCode()}");
        
                
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