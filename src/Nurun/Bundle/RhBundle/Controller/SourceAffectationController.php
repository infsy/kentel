<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\SourceAffectation;
use Nurun\Bundle\RhBundle\Form\SourceAffectationType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SourceAffectationController extends Controller
{
    /**
   * @Security("has_role('ROLE_ROOT')")
   */
//    Affichage de la liste des Types
   public function indexAction()
  {


       $listSources = $this->getDoctrine()
           ->getRepository('NurunRhBundle:SourceAffectation')
           ->findAll();

       return $this->render(
           'NurunRhBundle:SourceAffectation:index.html.twig', array(
           'sources' => $listSources));

  }  
  
  /**
   * @Security("has_role('ROLE_ROOT')")
   */
  public function addAction(Request $request)
  {

    // On crée un objet Mandat
    $source = new SourceAffectation();

    // On crée le FormBuilder grâce au service form factory
    $form = $this->get('form.factory')->create(new SourceAffectationType(), $source);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($source);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Source bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_sourceAffectation_home'));
    }

    return $this->render('NurunRhBundle:SourceAffectation:add.html.twig', array(
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
    $source = $em->getRepository('NurunRhBundle:SourceAffectation')->find($id);
    $form = $this->get('form.factory')->create(new SourceAffectationType(), $source);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($source);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Source bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_sourceAffectation_home'));
    }

    return $this->render('NurunRhBundle:SourceAffectation:edit.html.twig', array(
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
    $source = $em->getRepository('NurunRhBundle:SourceAffectation')->find($id);

    if (null === $source) {
      throw new NotFoundHttpException("La source d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($source);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "La source a bien été supprimée.");

      return $this->redirect($this->generateUrl('nurun_sourceAffectation_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('NurunRhBundle:SourceAffectation:delete.html.twig', array(
      'source' => $source,
      'form'   => $form->createView()
    ));
  } 
}


