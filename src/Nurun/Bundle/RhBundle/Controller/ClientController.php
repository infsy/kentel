<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Writer\DoctrineWriter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\Client;
use Nurun\Bundle\RhBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Export\CSVExport;
use APY\DataGridBundle\Grid\Export\ExcelExport;
use APY\DataGridBundle\Grid\Action\RowAction;

class ClientController extends Controller
{
    /**
   * @Security("has_role('ROLE_ADMIN')")
   */
//    Affichage de la liste des conseillers
   public function indexAction()
  {

       $clients = $this->getDoctrine()
           ->getRepository('NurunRhBundle:Client')
           ->findAll();

       return $this->render(
           'NurunRhBundle:Client:index.html.twig', array(
           'clients' => $clients));

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
            $logger->info('Nous avons récupéré le logger');
            
            // Create and configure the reader
            $logger->info('chargement de '.$document->getAbsolutePath());

            $file = new \SplFileObject($document->getAbsolutePath());
            $csvReader = new CsvReader($file);       
            $logger->info('comprend x lignes '.$csvReader->count());
            
            // Tell the reader that the first row in the CSV file contains column headers
            $csvReader->setHeaderRowNumber(0);

            foreach ($csvReader as $row) { 
                $client = $em->getRepository('NurunRhBundle:Client')
                  ->findOneByAcronyme($row['acronyme']);
                if (empty($client))
                {
                    $client = new Client();
                    $client->setAcronyme($row['acronyme']);
                    $client->setIdentifiant($row['identifiant']);
                }
                else
                {
                    $client->setIdentifiant($row['identifiant']);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
            }
           
            $this->redirect($this->generateUrl('nurun_client_home'));
        }
    }

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('NurunRhBundle:Client:upload.html.twig', array(
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
      ->getRepository('NurunRhBundle:Client')
    ;
    
    // On récupère l'entité correspondante à l'id $id
    $client = $repository->find($id);
    
    // $conseiller est donc une instance de Nurun\RhBundle\Entity\Conseiller
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $client) {
      throw new NotFoundHttpException("Le client d'id ".$id." n'existe pas.");
    }
    $mandats = $client->getMandats();
    return $this->render(
   'NurunRhBundle:Client:view.html.twig', array('client' => $client, 'mandats' => $mandats));

  }

   /**
   * @Security("has_role('ROLE_GESTION')")
   */
//  Création d'un conseiller
  public function addAction(Request $request)
  {
    // On crée un objet Mandat
    $client = new Client();

    // On crée le FormBuilder grâce au service form factory
    $formBuilder = $this->get('form.factory')->createBuilder('form', $client);

    // On ajoute les champs de l'entité que l'on veut à notre formulaire
    $formBuilder
      ->add('acronyme',      'text')
      ->add('identifiant',     'text')
    ;
        
    $formBuilder
      ->add('save',      'submit')
    ;
    
    // À partir du formBuilder, on génère le formulaire
    $form = $formBuilder->getForm();

    // On fait le lien Requête <-> Formulaire
    // À partir de maintenant, la variable $client contient les valeurs entrées dans le formulaire par le visiteur
    $form->handleRequest($request);

    // On vérifie que les valeurs entrées sont correctes
    // (Nous verrons la validation des objets en détail dans le prochain chapitre)
    if ($form->isValid()) {
      // On l'enregistre notre objet $advert dans la base de données, par exemple
      $em = $this->getDoctrine()->getManager();
      $em->persist($client);
      $em->flush();
      $logger = $this->get('logger');
      $user = $this->getUser();
      $logger->info('Ajout du client'.$client->getAcronyme() .' par '.$user->getUserName());
      $request->getSession()->getFlashBag()->add('notice', 'Nouveau client bien enregistré.');

      // On redirige vers la page de visualisation du mandat nouvellement créée
      return $this->redirect($this->generateUrl('nurun_client_view', array('id' => $client->getId())));
    }
    
    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('NurunRhBundle:Client:add.html.twig', array(
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
    $client = $em->getRepository('NurunRhBundle:Client')->find($id);

    if (null === $client) {
      throw new NotFoundHttpException("Le client d'id ".$id." n'existe pas.");
    }
    
    // On crée le FormBuilder grâce au service form factory
    $formBuilder = $this->get('form.factory')->createBuilder('form', $client);

    // On ajoute les champs de l'entité que l'on veut à notre formulaire
    $formBuilder
      ->add('acronyme',      'text')
      ->add('identifiant',     'text')
    ;
        
    $formBuilder
      ->add('save',      'submit')
    ;
    
    // À partir du formBuilder, on génère le formulaire
    $form = $formBuilder->getForm();
    
//    $form = $this->createForm(new ClientEditType(), $client);

    if ($form->handleRequest($request)->isValid()) {
      // Inutile de persister ici, Doctrine connait déjà notre annonce
      $em->flush();
      $logger = $this->get('logger');
      $user = $this->getUser();
      $logger->info('MAJ du client'.$client->getAcronyme() .' par '.$user->getUserName());
      $request->getSession()->getFlashBag()->add('notice', 'Nouveau client bien enregistré.');
      $request->getSession()->getFlashBag()->add('notice', 'Client bien modifié.');

      return $this->redirect($this->generateUrl('nurun_client_view', array('id' => $client->getId())));
    }

    return $this->render('NurunRhBundle:Client:edit.html.twig', array(
      'form'   => $form->createView(),
      'client' => $client // Je passe également l'annonce à la vue si jamais elle veut l'afficher
    ));
  }
  
 /**
   * @Security("has_role('ROLE_GESTION')")
   */
  public function deleteAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $client = $em->getRepository('NurunRhBundle:Client')->find($id);

    if (null === $client) {
      throw new NotFoundHttpException("Le client d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $logger = $this->get('logger');
      $user = $this->getUser();
      $logger->info('Suppression du client'.$client->getAcronyme() .' par '.$user->getUserName());
      $request->getSession()->getFlashBag()->add('notice', 'Nouveau client bien enregistré.');
      $em->remove($client);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "Le client a bien été supprimé.");

      return $this->redirect($this->generateUrl('nurun_client_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('NurunRhBundle:Client:delete.html.twig', array(
      'client' => $client,
      'form'   => $form->createView()
    ));
  }
  
}

