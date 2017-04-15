<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Writer\DoctrineWriter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\Diplome;
use Nurun\Bundle\RhBundle\Form\DiplomeType;
use Nurun\Bundle\RhBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Export\CSVExport;
use APY\DataGridBundle\Grid\Export\ExcelExport;
use APY\DataGridBundle\Grid\Action\RowAction;

class DiplomeController extends Controller
{
    /**
   * @Security("has_role('ROLE_RDP')")
   */
//    Affichage de la liste des conseillers
   public function indexAction()
  {

      $listDiplomes = $this->getDoctrine()
          ->getRepository('NurunRhBundle:Diplome')
          ->findAll();

      return $this->render(
          'NurunRhBundle:Diplome:index.html.twig', array(
          'diplomes' => $listDiplomes));
  }  

  /**
   * @Security("has_role('ROLE_ROOT')")
   */
  // Chargement d'un fichier de clients
 public function uploadAction()
  {
     // On crée un objet Mandat
    $document = new Document();

    $form = $this->createFormBuilder($document)
        ->add('name')
        ->add('file')
        ->getForm()
    ;

    if ($this->getRequest()->isMethod('POST')) {
        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
                        
            $em->persist($document);
            $em->flush();
            $logger = $this->get('logger');
            
            // Create and configure the reader
            $logger->info('chargement de '.$document->getAbsolutePath());

            $file = new \SplFileObject($document->getAbsolutePath());
            $csvReader = new CsvReader($file);       
            $logger->info('comprend x lignes '.$csvReader->count());
            
            // Tell the reader that the first row in the CSV file contains column headers
            $csvReader->setHeaderRowNumber(0);

            foreach ($csvReader as $row) { 
                $diplome = $em->getRepository('NurunRhBundle:Diplome')
                  ->findOneByDescription($row['description']);
                if (empty($diplome))
                {
                    $diplome = new Diplome();
                    $diplome->setDescription($row['description']);
                    $diplome->setNiveau($row['niveau']);
                }
                else
                {
                    $diplome->setNiveau($row['niveau']);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($diplome);
                $em->flush();
            }
           
            $this->redirect($this->generateUrl('nurun_diplome_home'));
        }
    }

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('NurunRhBundle:Diplome:upload.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  
  /**
   * @Security("has_role('ROLE_ADMIN')")
   */
  // Affichage du détail d'un client
  public function viewAction($id)
  {
      // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('NurunRhBundle:Diplome')
    ;
    
    // On récupère l'entité correspondante à l'id $id
    $diplome = $repository->find($id);
    
    // $conseiller est donc une instance de Nurun\RhBundle\Entity\Conseiller
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $diplome) {
      throw new NotFoundHttpException("Le diplome d'id ".$id." n'existe pas.");
    }
    $conseillers = $diplome->getConseillers();
    return $this->render(
   'NurunRhBundle:Diplome:view.html.twig', array('diplome' => $diplome, 'conseillers' => $conseillers));

  }

  
  /**
   * @Security("has_role('ROLE_GESTION')")
   */
//  Création d'un conseiller
  public function addAction(Request $request)
  {
    // On crée un objet Mandat
    $diplome = new Diplome();

    $form = $this->get('form.factory')->create(new DiplomeType(), $diplome);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($diplome);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Diplôme bien enregistré.');

      return $this->redirect($this->generateUrl('nurun_diplome_view', array('id' => $diplome->getId())));
    }
    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('NurunRhBundle:Diplome:add.html.twig', array(
      'form' => $form->createView(),
    ));
      
 }
  
 /**
   * @Security("has_role('ROLE_GESTION')")
   */
  public function editAction($id, Request $request)
  {
 
    $em = $this->getDoctrine()->getManager();

    // On récupère le client $id
    $diplome = $em->getRepository('NurunRhBundle:Diplome')->find($id);

    if (null === $diplome) {
      throw new NotFoundHttpException("Le diplome d'id ".$id." n'existe pas.");
    }
    $form = $this->get('form.factory')->create(new DiplomeType(), $diplome);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($diplome);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Diplôme bien enregistré.');

      return $this->redirect($this->generateUrl('nurun_diplome_view', array('id' => $diplome->getId())));
    }

    return $this->render('NurunRhBundle:Diplome:edit.html.twig', array(
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
    $diplome = $em->getRepository('NurunRhBundle:Diplome')->find($id);

    if (null === $diplome) {
      throw new NotFoundHttpException("Le diplome d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $logger = $this->get('logger');
      $user = $this->getUser();
      $logger->info('Suppression du diplome'.$diplome->getDescription() .' par '.$user->getUserName());
      $request->getSession()->getFlashBag()->add('notice', 'Diplome bien supprimé.');
      $em->remove($diplome);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "Le diplôme a bien été supprimé.");

      return $this->redirect($this->generateUrl('nurun_diplome_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('NurunRhBundle:Diplome:delete.html.twig', array(
      'diplome' => $diplome,
      'form'   => $form->createView()
    ));
  }
  
}

