<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use CodigoAustral\MPCUtilsBundle\Service\MpcResourcesService;

class UpdatePHACommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('mpc:download:pha')
                ->setDescription('Downloads the MPC Potential Hazardous Asteroids list into the local file repo');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        // get the PHA web page from parameters file
        
        /* @var $service MpcResourcesService */
        $service=$this->getContainer()->get('mpcbundle.mpcresources');
        
        
        try {
            $result=$service->downloadLongPHAlist();
            $output->writeln("Downloaded {$result['bytes']} bytes from resource {$result['url']}");
        }
        catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
        
        
        
        
    }

}
