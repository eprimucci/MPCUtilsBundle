<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Input\ArrayInput;

use CodigoAustral\MPCUtilsBundle\Entity\Observatory;

class PeriodicObservationsUpdateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('mpc:cron:observations')
            ->setDescription('Process MPC observations data for a given date')
                ->addArgument('today', InputArgument::OPTIONAL, 'Start date YYYY-MM-DD, assumed today if null');
    }
    


    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $today=new \DateTime($input->getArgument('today'));
        
        $em=$this->getContainer()->get('doctrine.orm.entity_manager');
        
        // required commands
        $downloader = $this->getApplication()->find('mpc:observations:download');
        $loader = $this->getApplication()->find('mpc:observations:parse-and-load');
        
        
        
        // get observatories:
        /* @var $obs ArrayCollection */
        $obs = $em->getRepository('CodigoAustralMPCUtilsBundle:Observatory')
                ->findAllObservatoryInDownloadQueue($today);

        
        $requestedTimestamp=mktime(0, 0, 0, 
                intval($today->format('M')), 
                intval($today->format('j')), 
                intval($today->format('Y')));
        
        if($obs==null) {
            $this->getContainer()->get('logger')
                        ->warn($this->getName().": No Observatories found for this batch");
            return;
        }
        
        
        /* @var $observatory Observatory */
        foreach($obs as $observatory) {
            
            $lastObsTimestamp=mktime(0, 0, 0, 
                    intval($observatory->getLastObsDownload()->format('M')), 
                    intval($observatory->getLastObsDownload()->format('j')), 
                    intval($observatory->getLastObsDownload()->format('Y')));
            
            
            if($lastObsTimestamp>=$requestedTimestamp) {
                $this->getContainer()->get('logger')
                        ->warn($this->getName().": {$observatory->getCode()} already has info for {$today->format('Y-m-d')}. Last obs: {$observatory->getLastObsDownload()->format('Y-m-d')}");
                continue;
            }
            
            
            $downloaderArguments = array(
                'command' => 'mpc:observations:download',
                'code'=>$observatory->getCode(), 
                'start'=>$observatory->getLastObsDownload()->format('Y-m-d'),
                'end'=>$today->format('Y-m-d'),
                '--forcedownload'=>false,
                );
            
            $parserArguments = array(
                'command' => 'mpc:observations:parse-and-load',
                'code'=>$observatory->getCode(), 
                'start'=>$observatory->getLastObsDownload()->format('Y-m-d'),
                'end'=>$today->format('Y-m-d'),
                );
            
            $this->getContainer()->get('logger')->info($this->getName().": Processing {$observatory->getCode()} with ".  var_export($downloaderArguments, true));
            $output->writeln(date('Y-m-d G:i:s', time())." Processing {$observatory->getCode()}");
            try {
                
                // get the observations
                $downloader->run(new ArrayInput($downloaderArguments), $output);
                
                // now parse and load them
                $loader->run(new ArrayInput($parserArguments), $output);
                
                // update dates
                $observatory->setLastObsDownload($today);
                
                $this->getContainer()->get('logger')->info($this->getName().": Successfully updated {$observatory->getCode()}");
            
                
            }
            catch (\Exception $e) {
                $this->getContainer()->get('logger')->warn($this->getName().': '.$e->getMessage());
            }
            
            $em->flush();
        }
        

        
    }

}
