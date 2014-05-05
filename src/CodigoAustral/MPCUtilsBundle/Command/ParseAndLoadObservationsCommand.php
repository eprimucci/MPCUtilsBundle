<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use CodigoAustral\MPCUtilsBundle\Service\MpcResourcesService;

class ParseAndLoadObservationsCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('mpc:observations:parse-and-load')
            ->setDescription('Parses and load into local DB selected the Observations')
            ->addArgument('code', InputArgument::REQUIRED, 'MPC Code')
            ->addArgument('start', InputArgument::REQUIRED, 'Start date YYYY-MM-DD')
            ->addArgument('end', InputArgument::REQUIRED, 'End date YYYY-MM-DD')
        ;
    }
    


    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $code=$input->getArgument('code');
        $startDate=$input->getArgument('start');
        $endDate=$input->getArgument('end');
        
        
        $em=$this->getContainer()->get('doctrine.orm.entity_manager');
        
        /* @var $observatory Observatory */
        $observatory  = $em->getRepository('CodigoAustralMPCUtilsBundle:Observatory')->findOneByCode($code);
        
        if($observatory==null) {
            throw new \Exception('No such observatory');
        }
        
        /* @var $service MpcResourcesService */
        $service=$this->getContainer()->get('mpcbundle.mpcresources');
        
        try {
            
            // now load the observations
            $targetFile=$service->buildObservationsLocalFilename($observatory, $startDate, $endDate);
            
            $service->parseAndLoadRawObservationsFile($observatory, $targetFile);
        }
        catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
    }

}
