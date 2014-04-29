<?php

namespace QRsafe\MvpBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use QRsafe\MvpBundle\Entity\Inspectionform;
use QRsafe\MvpBundle\Entity\Inspectable;
use QRsafe\MvpBundle\Form\InspectionformType;
use QRsafe\MvpBundle\Entity\Question;
use Doctrine\Common\Collections\ArrayCollection;
use QRsafe\MvpBundle\Entity\Preloadedinspectionform;
use QRsafe\MvpBundle\Entity\Preloadedquestion;
/**
 * Inspectionform controller.
 *
 * @Route("/inspectionform")
 */
class InspectionformController extends BaseController {

    /**
     * Lists all Inspectionforms for an Inspectable
     *
     * @Route("/inspectable/{inspectable}", name="inspectionform_by_inspectable")
     * @Method("GET")
     * @Template()
     */
    public function listsByInspectableAction(Inspectable $inspectable) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        if($inspectable==null) {
            return $this->showNotFound();
        }
        
        if($inspectable->getCustomer()!=$this->getCurrentCustomer()) {
            return $this->showOwnershipMessage('The selected Inspectable does not belong to your account!');
        }  
        
        if($inspectable->getInspectionforms()->count()==0) {
            return $this->redirect($this->generateUrl('inspectable_addforms', array('id'=>$inspectable->getId())));
        }
        
        return array('inspectable'=>$inspectable);
    }

    
    
    /**
     * Lists all Inspectionform entities for the requesting customer.
     *
     * @Route("/", name="inspectionform")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        $inspectionforms = $this->getDoctrine()->getManager()
                ->getRepository('QRsafeMvpBundle:Inspectionform')
                ->findByCustomer($this->getCurrentCustomer());

        return array('entities' => $inspectionforms,);
    }
    
    
    /**
     * Lists all Inspectionform entities for the requesting customer.
     *
     * @Route("/defaults", name="inspectionform_defaults")
     * @Method("GET")
     * @Template()
     */
    public function defaultsAction() {
        
        $r=$this->get('qrsafe.inspectionformservice')->getRepositoryInspectionForms(true);
        
        return new Response(json_encode($r),200);
        
        
    }
    

    /**
     * Creates a new Inspectionform entity.
     *
     * @Route("/", name="inspectionform_create")
     * @Method("POST")
     * @Template("QRsafeMvpBundle:Inspectionform:new.html.twig")
     */
    public function createAction(Request $request) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        /* @var $inspectionform Inspectionform */
        $inspectionform = $this->get('qrsafe.inspectionformservice')->getNew($this->getCurrentCustomer());
        
        $form = $this->createCreateForm($inspectionform);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $inspectionform->setCustomer($this->getCurrentCustomer());
            $em->persist($inspectionform);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Your new Inspection form was created.');
            
            return $this->redirect($this->generateUrl('inspectionform'));
        }

        return array(
            'entity' => $inspectionform,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Inspectionform entity.
     *
     * @param Inspectionform $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Inspectionform $entity) {
        $form = $this->createForm(new InspectionformType(), $entity, array(
            'action' => $this->generateUrl('inspectionform_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Save new Inspection form', 'attr'=>array('class'=>'btn btn-info'), ));

        return $form;
    }

    /**
     * Displays a form to create a new Inspectionform entity.
     *
     * @Route("/new", name="inspectionform_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        $inspectionform = new Inspectionform();
        $q1=new Question();
        $q1->setInspectionform($inspectionform);
        $q1->setCreated(new \DateTime());
        $q1->setType(Question::QUESTION_TICK);
        $inspectionform->getQuestions()->add($q1);
        
        $form = $this->createCreateForm($inspectionform);

        return array(
            'entity' => $inspectionform,
            'form' => $form->createView(),
        );
    }
    
    
    /**
     * Presents a list of forms that can be imported to the current account
     *
     * @Route("/newfromtemplate", name="inspectionform_new_from_template")
     * @Method("GET")
     * @Template()
     */
    public function newFromTemplateAction() {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        return array('inspectionforms'=>$this->get('qrsafe.inspectionformservice')->getRepositoryInspectionFormsAsArray());

    }


    /**
     * Presents a list of forms that can be imported to the current account
     *
     * @Route("/createfromtemplate", name="inspectionform_create_from_template")
     * @Method("POST")
     */
    public function createFromTemplateAction(Request $request) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        $data=$request->request->all();
        
        if(!isset($data['template'])) {
            return Response('Missing template. Tampering?', 500);
        }
        
        /* @var $inspectionform Inspectionform */
        $inspectionform = $this->getDoctrine()->getManager()
                ->getRepository('QRsafeMvpBundle:Inspectionform')
                ->find($data['template']);
        
        if($inspectionform==null) {
            return $this->showNotFound();
        }

        $service=$this->get('qrsafe.inspectionformservice');
        
        // validate that this form belongs to the public repo form
        if($inspectionform->getCustomer()->getId() != $service->getRepoCustomer()) {
            return Response('Selected template is not public! Tampering?', 405);
        }
        
        // ok, now do the copy!
        $newId=$service->importInspectionForm($inspectionform, $this->getCurrentUser());
        
        return $this->redirect($this->generateUrl('inspectionform_edit', array('id'=>$newId)));

    }
    
    
    /**
     * Finds and displays a Inspectionform entity.
     *
     * @Route("/{id}", name="inspectionform_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        $em = $this->getDoctrine()->getManager();

        $inspectionform = $em->getRepository('QRsafeMvpBundle:Inspectionform')->find($id);

        if($inspectionform==null) {
            return $this->showNotFound();
        }        
        if($inspectionform->getCustomer()!=$this->getCurrentCustomer()) {
            return $this->showOwnershipMessage('The selected Inspection form does not belong to your account!');
        }

        return array(
            'entity' => $inspectionform,
        );
    }

    /**
     * Displays a form to edit an existing Inspectionform entity.
     *
     * @Route("/{id}/edit", name="inspectionform_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        $em = $this->getDoctrine()->getManager();

        $inspectionform = $em->getRepository('QRsafeMvpBundle:Inspectionform')->find($id);

        if($inspectionform==null) {
            return $this->showNotFound();
        }        
        if($inspectionform->getCustomer()!=$this->getCurrentCustomer()) {
            return $this->showOwnershipMessage('The selected Inspection form does not belong to your account!');
        }

        $editForm = $this->createEditForm($inspectionform);

        return array(
            'entity' => $inspectionform,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Inspectionform entity.
     *
     * @param Inspectionform $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Inspectionform $entity) {
        $form = $this->createForm(new InspectionformType(), $entity, array(
            'action' => $this->generateUrl('inspectionform_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr'=>array('class'=>'btn btn-info'), ));

        return $form;
    }

    /**
     * Edits an existing Inspectionform entity.
     *
     * @Route("/{id}", name="inspectionform_update")
     * @Method("PUT")
     * @Template("QRsafeMvpBundle:Inspectionform:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        $em = $this->getDoctrine()->getManager();

        /* @var $inspectionform Inspectionform */
        $inspectionform = $em->getRepository('QRsafeMvpBundle:Inspectionform')->find($id);

        if($inspectionform==null) {
            return $this->showNotFound();
        }        
        if($inspectionform->getCustomer()!=$this->getCurrentCustomer()) {
            return $this->showOwnershipMessage('The selected Inspection form does not belong to your account!');
        }

        $originalQuestions = new ArrayCollection();

        foreach ($inspectionform->getQuestions() as $question) {
            $originalQuestions->add($question);
        }
        
        $editForm = $this->createEditForm($inspectionform);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            // remove the relationship between the tag and the Task
            /* @var $question Question */
            foreach ($originalQuestions as $question) {
                if (false === $inspectionform->getQuestions()->contains($question)) {
                    $question->setInspectionform(null);
                    $em->persist($question);
                    $em->remove($question);
                }
            }

            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Your Inspection form changes were saved.');
            return $this->redirect($this->generateUrl('inspectionform'));
        }

        return array(
            'entity' => $inspectionform,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Inspectionform entity.
     *
     * @Route("/{id}", name="inspectionform_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return $this->showPermissionMessage();
        }
        
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inspectionform = $em->getRepository('QRsafeMvpBundle:Inspectionform')->find($id);

            if($inspectionform==null) {
                return $this->showNotFound();
            }        
            if($inspectionform->getCustomer()!=$this->getCurrentCustomer()) {
                return $this->showOwnershipMessage('The selected Inspection form does not belong to your account!');
            }

            $em->remove($inspectionform);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('inspectionform'));
    }

    /**
     * Creates a form to delete a Inspectionform entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('inspectionform_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    
    /**
     * Delete one Inspectionform/Form via Ajax
     *
     * @Route("/{id}/ajax/delete", name="inspectionform_ajax_delete", options={"expose"=true})
     * @Method("DELETE")
     */
    public function ajaxDelete($id) {
        
        $em = $this->getDoctrine()->getEntityManager();
        
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            return new \Symfony\Component\HttpFoundation\Response('Restricted', 405);
        }
        
        /* @var $inspectionform Inspectionform */
        $inspectionform = $em->getRepository('QRsafeMvpBundle:Inspectionform')->find($id);
        
        if($inspectionform==null) {
            return new \Symfony\Component\HttpFoundation\Response('Not found', 404);
        }        
        if($inspectionform->getCustomer()!=$this->getCurrentCustomer()) {
            return new \Symfony\Component\HttpFoundation\Response('Forbidden', 403);
        }        
        if(!$inspectionform->getInspectables()->isEmpty()) {
            // we have one or more inspecatbles using this form, show them
            /* @var $inspectable Inspectable */
            $reasons=array();
            foreach($inspectionform->getInspectables() as $inspectable) {
                $reasons[]=array('name'=>$inspectable->getName(), 'id'=>$inspectable->getId());
            }
            return new \Symfony\Component\HttpFoundation\Response(json_encode($reasons), 405);
        }
        $em->remove($inspectionform);
        $em->flush();
        
        return new \Symfony\Component\HttpFoundation\Response('OK',200);
    }        
    
    
    /**
     * Toggle "importability"
     *
     * @Route("/{id}/ajax/toggleimport", name="inspectionform_ajax_toggleimport", options={"expose"=true})
     * @Method("PUT")
     */
    public function ajaxToggleImport($id) {
        
        $em = $this->getDoctrine()->getEntityManager();
        
        if (false === $this->get('security.context')->isGranted('ROLE_SUPERADMIN')) {
            return new \Symfony\Component\HttpFoundation\Response('Restricted', 405);
        }
        
        /* @var $inspectionform Inspectionform */
        $inspectionform = $em->getRepository('QRsafeMvpBundle:Inspectionform')->find($id);
        
        if($inspectionform==null) {
            return new \Symfony\Component\HttpFoundation\Response('Not found', 404);
        }        
        if($inspectionform->getCustomer()!=$this->getCurrentCustomer()) {
            return new \Symfony\Component\HttpFoundation\Response('Forbidden', 403);
        }        

        if($inspectionform->isImportable()) {
            $inspectionform->setImportable(false);
            $r='I0';
        }
        else {
            $inspectionform->setImportable(true);
            $r='I1';
        }
        
        $em->flush();
        
        return new \Symfony\Component\HttpFoundation\Response($r , 200);
    }        
    

    
}
