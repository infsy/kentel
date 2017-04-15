<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\TypeBesoin;
use Nurun\Bundle\RhBundle\Form\TypeBesoinType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TypeBesoinController extends Controller
{
    /**
   * @Security("has_role('ROLE_ROOT')")
   */
//    Affichage de la liste des Types
   public function indexAction()
  {


       $listTypes = $this->getDoctrine()
           ->getRepository('NurunRhBundle:TypeBesoin')
           ->findAll();

       return $this->render(
           'NurunRhBundle:TypeBesoin:index.html.twig', array(
           'types' => $listTypes));

  }  
  
  /**
   * @Security("has_role('ROLE_ROOT')")
   */
  public function addAction(Request $request)
  {

    // On crée un objet Mandat
    $type = new TypeBesoin();

    // On crée le FormBuilder grâce au service form factory
    $form = $this->get('form.factory')->create(new TypeBesoinType(), $type);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($type);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Type de besoin bien enregistré.');

      return $this->redirect($this->generateUrl('nurun_typeBesoin_home'));
    }

    return $this->render('NurunRhBundle:TypeBesoin:add.html.twig', array(
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
    $type = $em->getRepository('NurunRhBundle:TypeBesoin')->find($id);
    $form = $this->get('form.factory')->create(new TypeBesoinType(), $type);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($type);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Type de besoin bien enregistré.');

      return $this->redirect($this->generateUrl('nurun_typeBesoin_home'));
    }

    return $this->render('NurunRhBundle:TypeBesoin:edit.html.twig', array(
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
    $type = $em->getRepository('NurunRhBundle:TypeBesoin')->find($id);

    if (null === $type) {
      throw new NotFoundHttpException("Le type de besoin d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($type);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "Le type de besoin a bien été supprimé.");

      return $this->redirect($this->generateUrl('nurun_typeBesoin_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('NurunRhBundle:TypeBesoin:delete.html.twig', array(
      'type' => $type,
      'form'   => $form->createView()
    ));
  } 
}


