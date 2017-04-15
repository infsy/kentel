<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\ConseillerRdp;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Nurun\Bundle\RhBundle\Form\ConseillerRdpType;

class ConseillerRdpController extends Controller
{
    /**
   * @Security("has_role('ROLE_ADMIN')")
   */
  // Affichage de la liste des rdp
  public function indexAction($page)
  {
    if ($page < 1) {
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }
    
    $listConseillersRdp = $this->getDoctrine()->getRepository('NurunRhBundle:ConseillerRdp')->findAll();
    
    return $this->render('NurunRhBundle:ConseillerRdp:index.html.twig', array('listConseillersRdp' => $listConseillersRdp));
  }  

  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
  // Affichage du détail d'un rdp
  public function viewAction($id)
  {
    // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('NurunRhBundle:Conseiller')
    ;
    
    // On récupère les entité correspondante à l'id $id
    $rdp = $repository->find($id);
    
      // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('NurunRhBundle:ConseillerRdp')
    ;
    
    // On récupère les entité correspondante à l'id $id
    $conseillersrdp = $repository->findBy(
            array('rdp' => $id)
                );
    
    $i = 0;
    $conseillers = array();
    
    foreach ($conseillersrdp as $conseillerrdp) {
      $conseillerid = $conseillerrdp->getConseiller()->getId();
      $conseillerprenom = $conseillerrdp->getConseiller()->getPrenom();
      $conseillernom = $conseillerrdp->getConseiller()->getNom();
      
      $conseillerclient = $conseillerrdp->getConseiller()->getPrenom();
      $conseillermandat = $conseillerrdp->getConseiller()->getMandats();
      $conseillercp = $conseillerrdp->getConseiller()->getPrenom();

      $conseillers[$i] = array("id"=> $conseillerid,
        "prenom"=>$conseillerprenom ,
        "nom"=>$conseillernom,
        "client"=>$conseillerclient,
        "mandat"=>$conseillermandat,
        "cp"=>$conseillercp
        );
    }    
    
    // $conseiller est donc une instance de Nurun\RhBundle\Entity\Conseiller
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $conseillersrdp) {
      throw new NotFoundHttpException("Le rdp d'id ".$id." ne possède pas de ressources attitrées.");
    }
    return $this->render('NurunRhBundle:ConseillerRdp:view.html.twig', array('conseillersRdp' => $conseillersrdp, 'rdp' => $rdp));
  }
  
  /**
   * @Security("has_role('ROLE_GESTION')")
   */
  public function editAction($id, Request $request) {

    $em = $this->getDoctrine()->getManager();

    // On récupère le client $id
    $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);
    $conseillerRdp = new ConseillerRdp();
    $conseillerRdp->setConseiller($conseiller);
    $today = new \DateTime();
    $conseillerRdp->setDateDebut($today);

    // On récupère la page d'origine de cette requête pour y revenir en cas d'annulation
    $referer = $this->getRequest()->headers->get('referer');

    // return $this->redirect($referer);
    $form = $this->get('form.factory')->create(new ConseillerRdpType(), $conseillerRdp);
     
    if ($form->handleRequest($request)->isValid()) {
      $feuvert = $em->getRepository('NurunRhBundle:ConseillerRdp')->closeAllRdp($id, $conseillerRdp->getDateDebut());
      if ($feuvert){
        $em = $this->getDoctrine()->getManager();
        $em->persist($conseillerRdp);
        $em->flush();   
      }

      $request->getSession()->getFlashBag()->add('notice', 'Affectation de RDP bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $conseiller->getId())));
    }

    return $this->render('NurunRhBundle:ConseillerRdp:edit.html.twig', array(
                'form' => $form->createView(), 'referer' => $referer
    ));
  }

}

