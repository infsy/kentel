<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\ConseillerCompetence;
use Nurun\Bundle\RhBundle\Entity\Competence;
use Nurun\Bundle\RhBundle\Entity\Niveau;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Nurun\Bundle\RhBundle\Form\ConseillerCompetenceType;

class ConseillerCompetenceController extends Controller
{

//    Affichage de la liste des expertises d'un conseiller
    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function indexAction($id, Request $request)
    {

        $conseiller = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->find($id);

        $conseillerCompetences = $this->getDoctrine()
            ->getRepository('NurunRhBundle:ConseillerCompetence')
            ->findByConseiller($conseiller);

        $session = $this->getRequest()->getSession();
        $session->set('origine', $request->getUri());

        return $this->render(
            'NurunRhBundle:ConseillerCompetence:index.html.twig', array(
            'conseillerCompetences' => $conseillerCompetences,
            'id' => $id));

    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function addAction($id, Request $request)
    {

        // On crée un objet Mandat
        $conseillerCompetence = new ConseillerCompetence();
        $em = $this->getDoctrine()->getManager();
        // On récupère le conseiller $id
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);

        $conseillerCompetence->setConseiller($conseiller);

        // On crée le FormBuilder grâce au service form factory
        $form = $this->get('form.factory')->create(new ConseillerCompetenceType(), $conseillerCompetence);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseillerCompetence);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Expertise bien attribuée.');

            return $this->redirect($this->generateUrl('nurun_conseiller_competence_index', array('id' => $id)));
        }

        return $this->render('NurunRhBundle:ConseillerCompetence:add.html.twig', array(
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
        $conseillerCompetence = $em->getRepository('NurunRhBundle:ConseillerCompetence')->find($id);

        $form = $this->get('form.factory')->create(new ConseillerCompetenceType(), $conseillerCompetence);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseillerCompetence);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Modification bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_conseiller_competence_index', array(
                'id' => $conseillerCompetence->getConseiller()->getId())));
        }

        return $this->render('NurunRhBundle:ConseillerCompetence:edit.html.twig', array(
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
        $conseillerCompetence = $em->getRepository('NurunRhBundle:ConseillerCompetence')->find($id);

        if (null === $conseillerCompetence) {
            throw new NotFoundHttpException("Le ConseillerCompetence d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {
            $em->remove($conseillerCompetence);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "Cette expertise a bien été supprimée.");

            return $this->redirect($this->generateUrl('nurun_conseiller_competence_index', array(
                'id' => $conseillerCompetence->getConseiller()->getId())));
        }


        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('NurunRhBundle:ConseillerCompetence:delete.html.twig', array(
            'conseillerCompetence' => $conseillerCompetence,
            'form'   => $form->createView()
        ));
    }
}