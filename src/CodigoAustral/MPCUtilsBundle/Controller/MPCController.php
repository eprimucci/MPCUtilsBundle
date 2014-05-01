<?php

namespace CodigoAustral\MPCUtilsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * MPC controller.
 *
 * @Route("/mpc")
 */
class MPCController extends Controller {

    /**
     * Lists all Inspectionforms for an Inspectable
     *
     * @Route("/observatory", name="mpc_obs_list")
     * @Method("GET")
     * @Template()
     */
    public function observatoryListAction() {
        
        return new \Symfony\Component\HttpFoundation\Response('Hello!' , 200);
        
    }
    

    

    
}
