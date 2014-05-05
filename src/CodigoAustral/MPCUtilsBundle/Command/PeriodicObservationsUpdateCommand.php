<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use CodigoAustral\MPCUtilsBundle\Entity\Observatory;
use Symfony\Component\Console\Input\ArrayInput;

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
        $obs = $em->getRepository('CodigoAustralMPCUtilsBundle:Observatory')
                ->findAllObservatoryInDownloadQueue($today);

        
        $requestedTimestamp=mktime(0, 0, 0, $today->format('M'), $today->format('j'), $today->format('Y'));
        
        /* @var $observatory Observatory */
        foreach($obs as $observatory) {
            
            $lastObsTimestamp=mktime(0, 0, 0, $observatory->getLastObsDownload()->format('M'), $observatory->getLastObsDownload()->format('j'), $observatory->getLastObsDownload()->format('Y'));
            
            
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
            $this->getContainer()->get('logger')->info($this->getName().": Processing {$observatory->getCode()} with ".  var_export($downloaderArguments, true));
            try {
                $input = new ArrayInput($downloaderArguments);
                $downloader->run($input, $output);
                $this->getContainer()->get('logger')->info($this->getName().": Successfully updated {$observatory->getCode()}");
            }
            catch (\Exception $e) {
                $this->getContainer()->get('logger')->warn($this->getName().': '.$e->getMessage());
            }
        }
        

        
    }

}
