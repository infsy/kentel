<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\ConseillerCertification;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Nurun\Bundle\RhBundle\Form\ConseillerCertificationType;

class ConseillerCertificationController extends Controller
{

  //    Affichage de la liste des certifications d'un conseiller
      /**
       * @Security("has_role('ROLE_RDP')")
       */
      public function indexAction($id, Request $request)
      {

          $conseiller = $this->getDoctrine()
              ->getRepository('NurunRhBundle:Conseiller')
              ->find($id);

          $conseillerCertifications = $this->getDoctrine()
              ->getRepository('NurunRhBundle:conseillerCertification')
              ->findByConseiller($conseiller);

          $session = $this->getRequest()->getSession();
          $session->set('origine', $request->getUri());

          return $this->render(
              'NurunRhBundle:conseillerCertification:index.html.twig', array(
              'conseillerCertifications' => $conseillerCertifications,
              'id' => $id));

      }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction($id, Request $request)
    {

        // On crée un objet Mandat
        $ConseillerCertification = new ConseillerCertification();
        $em = $this->getDoctrine()->getManager();
        // On récupère le conseiller $id
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);

        $user = $this->get('security.context')->getToken()->getUser();
        $ConseillerCertification->setConseiller($conseiller);

        // On crée le FormBuilder grâce au service form factory
        $form = $this->get('form.factory')->create(new ConseillerCertificationType(), $ConseillerCertification);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ConseillerCertification);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Certification bien attribuée.');

            return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $id)));
        }

        return $this->render('NurunRhBundle:ConseillerCertification:add.html.twig', array(
            'form' => $form->createView(),
        ));

    }


        /**
         * @Security("has_role('ROLE_RDP')")
         */
        public function editAction($id, Request $request)
        {

            $em = $this->getDoctrine()->getManager();

            // On récupère le ConseillerCertification $id
            $ConseillerCertification = $em->getRepository('NurunRhBundle:ConseillerCertification')->find($id);

            $form = $this->get('form.factory')->create(new ConseillerCertificationType(), $ConseillerCertification);

            if ($form->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($ConseillerCertification);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Modification bien enregistrée.');

                return $this->redirect($this->generateUrl('nurun_conseiller_certification_index', array(
                    'id' => $ConseillerCertification->getConseiller()->getId())));
            }

            return $this->render('NurunRhBundle:ConseillerCertification:edit.html.twig', array(
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
                $conseillerCertification = $em->getRepository('NurunRhBundle:ConseillerCertification')->find($id);

                if (null === $conseillerCertification) {
                    throw new NotFoundHttpException("Le ConseillerCertification d'id ".$id." n'existe pas.");
                }

                // On crée un formulaire vide, qui ne contiendra que le champ CSRF
                // Cela permet de protéger la suppression d'annonce contre cette faille
                $form = $this->createFormBuilder()->getForm();

                if ($form->handleRequest($request)->isValid()) {
                    $em->remove($conseillerCertification);
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('info', "Cette certification a bien été supprimée.");

                    return $this->redirect($this->generateUrl('nurun_conseiller_certification_index', array(
                        'id' => $conseillerCertification->getConseiller()->getId())));
                }


                // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
                return $this->render('NurunRhBundle:ConseillerCertification:delete.html.twig', array(
                    'conseillerCertification' => $conseillerCertification,
                    'form'   => $form->createView()
                ));
            }
}
