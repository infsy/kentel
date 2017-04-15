<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Nurun\Bundle\RhBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommunController extends Controller
{

//    fonction choisir la VP sur laquelle travailler
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function chooseVpAction($target, Request $request)
    {
        $user = $this->getUser();

        $defaults = array(
            'Secteur' => $user->getVp()
        );

        $form = $this->createFormBuilder($defaults)
            ->add('Secteur', 'choice', array(
                'choices' => array('VPAS' => 'VPAS', 'VPTS' => 'VPTS', 'VPSI' => 'VPSI', 'TOUT' => 'Tout'),
                'required' => true,))
            ->add('Soumettre', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
//            $this->executiveByVpAction($data['Secteur']);
            return $this->redirect($this->generateUrl($target, array('vp' => $data['Secteur'])));
        }
        return $this->render('NurunRhBundle:Commun:choice.html.twig', array(
            'form' => $form->createView(),
        ));
    }


//    fonction choisir le profil sur laquelle travailler
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function chooseProfilAction($target, Request $request)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder()
            ->add('Profil', 'entity', array(
                'class' => 'NurunRhBundle:ProfilConseiller',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ProfilConseillerRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.profil', 'ASC')
                        ->select('p')->distinct(true);
                },
                'property' => 'display',
                'multiple' => false,
                'required' => false))
            ->add('Soumettre', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

//        On va travailler avec le $repository des conseillers
            $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('NurunRhBundle:ProfilConseiller');

//        On récupère l'id du profil
            $profil = $repository->find($data['Profil']);

            return $this->redirect($this->generateUrl($target, array('profilId' => $profil->getId())));
        }
        return $this->render('NurunRhBundle:Commun:choiceProfil.html.twig', array(
            'form' => $form->createView(),
        ));
    }
//    fonction choisir la VP et les dates sur laquelle travailler
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function chooseVpDatesAction($target, Request $request)
    {
        $user = $this->getUser();

        $defaults = array(
            'dateDeb' => new \DateTime(),
            'dateFin' => new \DateTime(),
            'Secteur' => $user->getVp()
        );

        $form = $this->createFormBuilder($defaults)
            ->add('dateDeb', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('dateFin', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('Secteur', 'choice', array(
                'choices' => array('VPAS' => 'VPAS', 'VPTS' => 'VPTS', 'VPSI' => 'VPSI', 'TOUT' => 'Tout'),
                'required' => true,))
            ->add('Soumettre', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
//            $this->executiveByVpAction($data['Secteur']);
            return $this->redirect($this->generateUrl($target, array('vp' => $data['Secteur'],
                'dateDeb' => $data['dateDeb']->format('Y-M-d'), 'dateFin' => $data['dateFin']->format('Y-M-d'))));
        }
        return $this->render('NurunRhBundle:Commun:choiceDates.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
