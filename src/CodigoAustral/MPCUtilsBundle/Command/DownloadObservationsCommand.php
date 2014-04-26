<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use CodigoAustral\MPCUtilsBundle\Service\MpcResourcesService;

class DownloadObservationsCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('mpc:download:observations')
                ->setDescription('Downloads the MPC Potential Hazardous Asteroids list into the local file repo');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        // get the PHA web page from parameters file
        
        /* @var $service MpcResourcesService */
        $service=$this->getContainer()->get('mpcbundle.mpcresources');
        
        
        try {
            $service->getObservations('Z96');
        }
        catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
        
    }

}
