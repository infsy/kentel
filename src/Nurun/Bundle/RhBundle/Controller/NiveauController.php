<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\Niveau;
use Nurun\Bundle\RhBundle\Form\NiveauType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class NiveauController extends Controller
{
    /**
   * @Security("has_role('ROLE_ROOT')")
   */
//    Affichage de la liste des Niveaux
   public function indexAction()
  {


       $listNiveaux = $this->getDoctrine()
           ->getRepository('NurunRhBundle:Niveau')
           ->findAll();

       return $this->render(
           'NurunRhBundle:Niveau:index.html.twig', array(
           'niveaux' => $listNiveaux));

  }  
  
  /**
   * @Security("has_role('ROLE_ROOT')")
   */
  public function addAction(Request $request)
  {

    // On crée un objet Mandat
    $niveau = new Niveau();

    // On crée le FormBuilder grâce au service form factory
    $form = $this->get('form.factory')->create(new NiveauType(), $niveau);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      if (empty($niveau->getForce()))
      {
        $niveau->setForce(1);
      }
      $em->persist($niveau);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Niveau bien enregistré.');

      return $this->redirect($this->generateUrl('nurun_niveau_home'));
    }

    return $this->render('NurunRhBundle:Niveau:add.html.twig', array(
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
    $niveau = $em->getRepository('NurunRhBundle:Niveau')->find($id);
    $form = $this->get('form.factory')->create(new NiveauType(), $niveau);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($niveau);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Niveau bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_niveau_home'));
    }

    return $this->render('NurunRhBundle:Niveau:edit.html.twig', array(
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
    $niveau = $em->getRepository('NurunRhBundle:Niveau')->find($id);

    if (null === $niveau) {
      throw new NotFoundHttpException("Le niveau d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($niveau);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "Le niveau a bien été supprimée.");

      return $this->redirect($this->generateUrl('nurun_niveau_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('NurunRhBundle:Niveau:delete.html.twig', array(
      'niveau' => $niveau,
      'form'   => $form->createView()
    ));
  } 
}


