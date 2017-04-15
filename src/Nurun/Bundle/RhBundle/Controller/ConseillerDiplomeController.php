<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\ConseillerDiplome;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Nurun\Bundle\RhBundle\Form\ConseillerDiplomeType;

class ConseillerDiplomeController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction($id, Request $request)
    {

        // On crée un objet Mandat
        $conseillerDiplome = new ConseillerDiplome();
        $em = $this->getDoctrine()->getManager();
        // On récupère le conseiller $id
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);

        $user = $this->get('security.context')->getToken()->getUser();
        $conseillerDiplome->setConseiller($conseiller);

        // On crée le FormBuilder grâce au service form factory
        $form = $this->get('form.factory')->create(new ConseillerDiplomeType(), $conseillerDiplome);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseillerDiplome);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Diplôme bien attribué.');

            return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $id)));
        }

        return $this->render('NurunRhBundle:ConseillerDiplome:add.html.twig', array(
            'form' => $form->createView(),
        ));

    }
}