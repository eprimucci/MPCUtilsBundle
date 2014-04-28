<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use CodigoAustral\MPCUtilsBundle\Service\MpcResourcesService;

class ImportObservatoriesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('mpc:download:observatories')
                ->setDescription('Imports observatories from the list published by MPC. Only for new installs!');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        // get the PHA web page from parameters file
        
        /* @var $service MpcResourcesService */
        $service=$this->getContainer()->get('mpcbundle.mpcresources');
        
        try {
            $obs=$service->getObservatories();
            var_dump($obs);
        }
        catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
        
    }

}
