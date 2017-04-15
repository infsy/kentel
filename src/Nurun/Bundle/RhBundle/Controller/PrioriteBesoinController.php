<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\PrioriteBesoin;
use Nurun\Bundle\RhBundle\Form\PrioriteBesoinType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PrioriteBesoinController extends Controller
{
    /**
   * @Security("has_role('ROLE_ROOT')")
   */
//    Affichage de la liste des Types
   public function indexAction()
  {


       $listPriorites = $this->getDoctrine()
           ->getRepository('NurunRhBundle:PrioriteBesoin')
           ->findAll();

       return $this->render(
           'NurunRhBundle:PrioriteBesoin:index.html.twig', array(
           'priorites' => $listPriorites));

  }  
  
  /**
   * @Security("has_role('ROLE_ROOT')")
   */
  public function addAction(Request $request)
  {

    // On crée un objet Mandat
    $priorite = new PrioriteBesoin();

    // On crée le FormBuilder grâce au service form factory
    $form = $this->get('form.factory')->create(new PrioriteBesoinType(), $priorite);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($priorite);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Priorité de besoin bien enregistré.');

      return $this->redirect($this->generateUrl('nurun_prioriteBesoin_home'));
    }

    return $this->render('NurunRhBundle:PrioriteBesoin:add.html.twig', array(
      'form' => $form->createView(),
    ));
    
  }

  /**
   * @Security("has_role('ROLE_ROOT')")
   */
  public function editAction($id, Request $request)
  {
 
    $em = $this->getDoctrine()->getManager();

    // On récupère le client $id
    $priorite = $em->getRepository('NurunRhBundle:PrioriteBesoin')->find($id);
    $form = $this->get('form.factory')->create(new PrioriteBesoinType(), $priorite);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($priorite);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Priorité de besoin bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_prioriteBesoin_home'));
    }

    return $this->render('NurunRhBundle:PrioriteBesoin:edit.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  
  /**
   * @Security("has_role('ROLE_GESTION')")
   */
public function deleteAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $priorite = $em->getRepository('NurunRhBundle:PrioriteBesoin')->find($id);

    if (null === $priorite) {
      throw new NotFoundHttpException("Le type de priorité d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($priorite);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "Le type de priorité a bien été supprimé.");

      return $this->redirect($this->generateUrl('nurun_prioriteBesoin_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('NurunRhBundle:PrioriteBesoin:delete.html.twig', array(
      'priorite' => $priorite,
      'form'   => $form->createView()
    ));
  } 
}


