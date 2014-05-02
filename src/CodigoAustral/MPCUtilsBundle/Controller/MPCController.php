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
        $em    = $this->getDoctrine()->getManager();
        $dql   = "SELECT o FROM CodigoAustralMPCUtilsBundle:Observatory o ORDER BY o.name";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20/*limit per page*/
        );

        // parameters to template
        return array('pagination' => $pagination);

        
    }
    

    /**
     * Lists all Inspectionforms for an Inspectable
     *
     * @Route("/observatory/{code}", name="mpc_obs_details")
     * @Method("GET")
     * @Template()
     */
    public function observatoryDetailsAction($code) {
        
        $observatory    = $this->getDoctrine()->getManager()
                ->getRepository('CodigoAustralMPCUtilsBundle:Observatory')->findOneByCode($code);
        return array('observatory'=>$observatory);
        
    }

    
}
