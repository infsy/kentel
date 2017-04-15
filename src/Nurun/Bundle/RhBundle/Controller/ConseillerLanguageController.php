<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\ConseillerLanguage;
use Nurun\Bundle\RhBundle\Entity\Language;
use Nurun\Bundle\RhBundle\Entity\Niveau;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Nurun\Bundle\RhBundle\Form\ConseillerLanguageType;

class ConseillerLanguageController extends Controller
{

//    Affichage de la liste des languages d'un conseiller
    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function indexAction(Request $request, $id)
    {

        $conseiller = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->find($id);

        $conseillerLanguages = $this->getDoctrine()
            ->getRepository('NurunRhBundle:ConseillerLanguage')
            ->findByConseiller($conseiller);

        $session = $this->getRequest()->getSession();
        $session->set('origine', $request->getUri());

        return $this->render(
            'NurunRhBundle:ConseillerLanguage:index.html.twig', array(
            'conseillerLanguages' => $conseillerLanguages,
            'id' => $id));

    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function addAction($id, Request $request)
    {

        // On crée un objet Mandat
        $conseillerLanguage = new ConseillerLanguage();
        $em = $this->getDoctrine()->getManager();
        // On récupère le conseiller $id
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);

        $conseillerLanguage->setConseiller($conseiller);

        // On crée le FormBuilder grâce au service form factory
        $form = $this->get('form.factory')->create(new ConseillerLanguageType(), $conseillerLanguage);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseillerLanguage);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Language bien attribué.');

            return $this->redirect($this->generateUrl('nurun_conseiller_language_index', array('id' => $id)));
        }

        return $this->render('NurunRhBundle:ConseillerLanguage:add.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function editAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        // On récupère le conseillerLanguage $id
        $conseillerLanguage = $em->getRepository('NurunRhBundle:ConseillerLanguage')->find($id);

        $form = $this->get('form.factory')->create(new ConseillerLanguageType(), $conseillerLanguage);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseillerLanguage);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Modification bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_conseiller_language_index', array(
                'id' => $conseillerLanguage->getConseiller()->getId())));
        }

        return $this->render('NurunRhBundle:ConseillerLanguage:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $conseillerLanguage = $em->getRepository('NurunRhBundle:ConseillerLanguage')->find($id);

        if (null === $conseillerLanguage) {
            throw new NotFoundHttpException("Le ConseillerLanguage d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {
            $em->remove($conseillerLanguage);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "Cette langue a bien été supprimée.");

            return $this->redirect($this->generateUrl('nurun_conseiller_language_index', array(
                'id' => $conseillerLanguage->getConseiller()->getId())));
        }


        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('NurunRhBundle:ConseillerLanguage:delete.html.twig', array(
            'conseillerLanguage' => $conseillerLanguage,
            'form'   => $form->createView()
        ));
    }
}