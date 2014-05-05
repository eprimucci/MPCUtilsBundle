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
        $this->setName('mpc:observations:download')
            ->setDescription('Downloads submitted observations and stores on local file')
            ->addArgument('code', InputArgument::REQUIRED, 'MPC Code')
            ->addArgument('start', InputArgument::REQUIRED, 'Start date YYYY-MM-DD')
            ->addArgument('end', InputArgument::REQUIRED, 'End date YYYY-MM-DD')
            ->addOption('forcedownload', null, InputOption::VALUE_NONE, 'If set, the file will be downloaded even if already present on disk.')
        ;
    }
    


    protected function execute(InputInterface $input, OutputInterface $output) {
        
        // get the PHA web page from parameters file
        $code=$input->getArgument('code');
        $startDate=$input->getArgument('start');
        $endDate=$input->getArgument('end');
        $overwrite=$input->getOption('forcedownload');
        
        
        $em=$this->getContainer()->get('doctrine.orm.entity_manager');
        
        /* @var $observatory Observatory */
        $observatory  = $em->getRepository('CodigoAustralMPCUtilsBundle:Observatory')->findOneByCode($code);
        
        if($observatory==null) {
            throw new \Exception('No such observatory');
        }
        
        /* @var $service MpcResourcesService */
        $service=$this->getContainer()->get('mpcbundle.mpcresources');
        
        try {
            
            $file=$service->getObservations($observatory, $overwrite, $startDate, $endDate);
            $this->getContainer()->get('logger')->info($this->getName().' '."Resource on disk now: {$file}");
            
        }
        catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
    }

}
