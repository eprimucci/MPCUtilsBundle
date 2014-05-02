<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use CodigoAustral\MPCUtilsBundle\Service\MpcResourcesService;

class DownloadObservationsCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('mpc:download:observations')
            ->setDescription('Downloads the MPC Potential Hazardous Asteroids list into the local file repo')
            ->addArgument('code', InputArgument::REQUIRED, 'MPC Code')
            ->addArgument('start', InputArgument::REQUIRED, 'Start date YYYY-MM-DD')
            ->addArgument('end', InputArgument::REQUIRED, 'End date YYYY-MM-DD')
            ->addOption('forcedownload', null, InputOption::VALUE_NONE, 'If set, the file will be downloaded even if already present on disk.')
        ;
    }
    

    private function parseUT($mpcDate) {
        
        // e.g. '2014 03 11.12548'
        
        $year=  substr($mpcDate, 0,4);
        $month = substr($mpcDate, 5,2);
        $resto=  substr($mpcDate, 8);
        
        
        
        
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $fecha='2014 03 11.12548';
        
        $d=new \DateTime($fecha);
        
        var_dump($d);
        die();
        
        
        // get the PHA web page from parameters file
        $code=$input->getArgument('code');
        $start=$input->getArgument('start');
        $end=$input->getArgument('end');
        $overwrite=$input->getOption('forcedownload');
        
        /* @var $service MpcResourcesService */
        $service=$this->getContainer()->get('mpcbundle.mpcresources');
        
        try {
            $service->getObservations($code, $overwrite, $start, $end);
        }
        catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
    }

}
