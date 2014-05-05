<?php

namespace CodigoAustral\MPCUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use CodigoAustral\MPCUtilsBundle\Entity\Observatory;


use CodigoAustral\MPCUtilsBundle\Service\MpcResourcesService;

class ImportObservatoriesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('mpc:observatories:download')
                ->setDescription('Imports observatories from the list published by MPC. Only for new installs!');
    }

    
    
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        // get the PHA web page from parameters file
        
        /* @var $service MpcResourcesService */
        $service=$this->getContainer()->get('mpcbundle.mpcresources');

        $em=$this->getContainer()->get('doctrine.orm.entity_manager');
        
        try {
            $observatories=$service->getObservatories();
            
            
            /*  [1830]=>
                    array(5) {
                      ["code"]=>
                      string(3) "Z99"
                      ["long"]=>
                      float(359.97874)
                      ["cos"]=>
                      float(0.595468)
                      ["sin"]=>
                      float(0.800687)
                      ["name"]=>
                      string(32) "Clixby Observatory, Cleethorpes
                  "
                    }
             */
            
            foreach($observatories as $obs) {
                $o=new Observatory();
                $o->setCode($obs['code']);
                $o->setCos(round($obs['cos'],6));
                $o->setLongitude(round($obs['long'],6));
                $o->setName($obs['name']);
                $o->setSin(round($obs['sin'],6));
                $o->setDownloadPriority(-1);
                $o->setLastObsDownload(new \DateTime('1500-01-01'));
                $em->persist($o);
            }
            
        }
        catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
        $em->flush();
        
        
    }

}
