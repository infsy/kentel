<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Writer\DoctrineWriter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\ConseillerMandat;
use Nurun\Bundle\RhBundle\Form\ConseillerMandatType;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Export\CSVExport;
use APY\DataGridBundle\Grid\Export\ExcelExport;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Vector;
use APY\DataGridBundle\Grid\Column;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use JMS\Serializer\SerializerBuilder;


class ConseillerMandatController extends Controller
{
    /**
   * @Security("has_role('ROLE_GESTION')")
   */
//    Affichage de la liste des affectations
   public function indexAction($page)
  {
       
    $gridManager = $this->get('grid.manager');
    
    
    $sourceIm = new Entity('NurunRhBundle:Conseiller');
    $grid0 = $gridManager->createGrid('im');
    $grid0->setSource($sourceIm);
    $grid0->setPermanentFilters(array(
    'mandats.statutAffectation' => array('operator' => 'eq', 'from' => 'I'),
        ));
    $grid0->hideColumns(array('poste', 'nbreHeures'));
    
    
    $source = new Entity('NurunRhBundle:ConseillerMandat');

    $grid1 = $gridManager->createGrid('mois1');    
    $grid2 = $gridManager->createGrid('mois2');    
    $grid3 = $gridManager->createGrid('mois3');    
    
    $grid1->setSource($source);
    $grid2->setSource($source);
    $grid3->setSource($source);
    
//    Récupération du premier et dernier jour du mois en cours
    $date = new \DateTime();
    $datefin = new \DateTime($date->format('Y-m').'-01');
    $datedeb = new \DateTime($date->format('Y-m').'-01');
    $mois = new \DateInterval('P1M');
    $jour = new \DateInterval('P1D');
    $jour->invert=1;
    $datefin->add($mois);
    $datefin->add($jour);
    
    $grid1->setPermanentFilters(array(
    'dateFin' => array('operator' => 'btw', 'from' => $datedeb->format('Y-m-d H:i:s'), 'to' => $datefin->format('Y-m-d H:i:s')),
//    'statutAffectation' => array('operator' => 'eq', 'from' => 'I'),
//    'mandat.identifiant' => array('operator' => 'eq', 'from' => 'Intermandat'),
        ));
    $rowAction = new RowAction("Voir", 'nurun_affectation_view', false, '_self', array('class' => 'grid_view_action'));
    $grid1->addRowAction($rowAction);
            
    //    Récupération de la période suivante
    $datedeb->add($mois);
    $datefin = new \DateTime($datedeb->format('Y-m-d'));
    $datefin->add($mois);
    $datefin->add($jour);

    $grid2->setPermanentFilters(array(
    'dateFin' => array('operator' => 'btw', 'from' => $datedeb->format('Y-m-d H:i:s'), 'to' => $datefin->format('Y-m-d H:i:s'))
        ));
    $grid2->addRowAction($rowAction);

    //    Récupération de la période suivante
    $datedeb->add($mois);
    $datefin = new \DateTime($datedeb->format('Y-m-d'));
    $datefin->add($mois);
    $datefin->add($jour);

    $grid3->setPermanentFilters(array(
    'dateFin' => array('operator' => 'btw', 'from' => $datedeb->format('Y-m-d H:i:s'), 'to' => $datefin->format('Y-m-d H:i:s'))
        ));
    $grid3->addRowAction($rowAction);

    if ($grid0->isReadyForRedirect() || $grid1->isReadyForRedirect() || $grid2->isReadyForRedirect() || $grid3->isReadyForRedirect() )
    {
        if ($grid0->isReadyForExport())
        {
            return $grid0->getExportResponse();
        }
        
        if ($grid1->isReadyForExport())
        {
            return $grid1->getExportResponse();
        }

        if ($grid2->isReadyForExport())
        {
            return $grid2->getExportResponse();
        }

        if ($grid3->isReadyForExport())
        {
            return $grid3->getExportResponse();
        }
        
        // Url is the same for the grids
        return new RedirectResponse($grid0->getRouteUrl());
    }
    else
    {
       return $this->render('NurunRhBundle:ConseillerMandat:index.html.twig', array('grid0' => $grid0, 'grid1' => $grid1, 'grid2' => $grid2, 'grid3' => $grid3));
    }

 }  
 
  public function loadAction()
  {
        $logger = $this->get('logger');
     
     // Create and configure the reader
    $file = new \SplFileObject('/home/cedric/affectations.csv');
    $csvReader = new CsvReader($file);
    $logger->info('fichier chargé');

    // Tell the reader that the first row in the CSV file contains column headers
    $csvReader->setHeaderRowNumber(0);
    $logger->info('Header positionné');
    $logger->info('compte : '.$csvReader->count());

    foreach ($csvReader as $row) { 
    $logger->info('on rentre dans la boucle avec '. $row['nom']);
        $affectation = new ConseillerMandat();
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')
        ->findOneByName($row['nom'],$row['prenom']);
        $affectation->setConseiller($conseiller);
        $affectation->setDateDebut($row['début']);
        $affectation->setDateFin($row['fin']);
        $affectation->setPourcentage($row['pourcent']);
        $mandat = $em->getRepository('NurunRhBundle:Mandat')
        ->findOneFromText($row['projet'],',');
        if (empty($mandat))
        {
            $mandat = $em->getRepository('NurunRhBundle:Mandat')
        ->findByIdentifiant($row['projet']);
        }
        
        $affectation->setMandat($mandat);
        if ($row['statut'] == 'Affectation') 
                {
            $affectation->setStatutAffectation('A');
                }
            else if ($row['statut'] == 'Proposition')
            {
            $affectation->setStatutAffectation('P');
            }
            else if ($row['statut'] == 'Prop. Stratégique')
            {
            $affectation->setStatutAffectation('S');
            }
            else if ($row['statut'] == 'Vacances')
            {
            $affectation->setStatutAffectation('V');
            }
            else if ($row['statut'] == 'Maladie')
            {
             $affectation->setStatutAffectation('M');   
            }
            else
            {
             $affectation->setStatutAffectation('?');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($affectation);
        $em->flush();        

    }

    return $this->render(
   'NurunRhBundle:ConseillerMandat:load.html.twig');
 }
 
  public function addAction(Request $request)
  {
    // On crée un objet Mandat
    $affectation = new ConseillerMandat();
    $affectation->setPourcentage('100');
    $affectation->setStatutAffectation('A');

    $form = $this->get('form.factory')->create(new ConseillerMandatType(), $affectation);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($affectation);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_affectation_view', array('id' => $affectation->getId())));
    }

    return $this->render('NurunRhBundle:ConseillerMandat:add.html.twig', array(
      'form' => $form->createView(),
    ));
    
    

  }
  
  // Affichage du détail d'une affectation
  public function viewAction($id)
  {
      // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('NurunRhBundle:ConseillerMandat')
    ;
    
    // On récupère l'entité correspondante à l'id $id
    $affectation = $repository->find($id);
    
    if (null === $affectation) {
      throw new NotFoundHttpException("L'affectation d'id ".$id." n'existe pas.");
    }
        
    return $this->render(
   'NurunRhBundle:ConseillerMandat:view.html.twig', array('affectation' => $affectation));

  }

  public function editAction($id, Request $request)
  {
   $em = $this->getDoctrine()->getManager();

    // On récupère le client $id
    $affectation = $em->getRepository('NurunRhBundle:ConseillerMandat')->find($id);
    $form = $this->get('form.factory')->create(new ConseillerMandatType(), $affectation);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($affectation);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_affectation_view', array('id' => $affectation->getId())));
    }

    return $this->render('NurunRhBundle:ConseillerMandat:edit.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  
public function deleteAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $affectation = $em->getRepository('NurunRhBundle:ConseillerMandat')->find($id);

    if (null === $affectation) {
      throw new NotFoundHttpException("L'affectation d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($affectation);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'affectation a bien été supprimée.");

      return $this->redirect($this->generateUrl('nurun_affectation_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('NurunRhBundle:ConseillerMandat:delete.html.twig', array(
      'affectation' => $affectation,
      'form'   => $form->createView()
    ));
  } 
}

//
//      $gridManager = $this->get('grid.manager');
//
//              
//       $list1mois = $this->getDoctrine()
//        ->getRepository('NurunRhBundle:ConseillerMandat')
//        ->findAffectationsWithDelay(0);
//       if (count($list1mois) == 0) 
//        {
//            $columns = array(
//                new Column\NumberColumn(array('id' => 'id0', 'field' => 'id', 'source' => true, 'primary' => true, 'title' => 'id')),
//                new Column\TextColumn(array('id' => 'prenom', 'field' => 'prenom', 'source' => true, 'title' => 'Prénom')),
//                new Column\TextColumn(array('id' => 'nom', 'field' => 'nom', 'source' => true, 'title' => 'Nom')),
//                new Column\TextColumn(array('id' => 'nummandat', 'field' => 'nummandat', 'source' => true, 'title' => 'Numéro de mandat')),
//                new Column\DateTimeColumn(array('id' => 'datefin', 'field' => 'datefin', 'source' => true, 'title' => 'Date de fin', 'format' => 'd/m/Y')),
//                );
//
//            $source1 = new Vector(array(), $columns); 
//        }
//       else
//       {
//           $source1 = new Vector($list1mois);
//           
//       }
//           $source1->setId(array('id','id0'));
//       
//           
//       $list2mois = $this->getDoctrine()
//        ->getRepository('NurunRhBundle:ConseillerMandat')
//        ->findAffectationsWithDelay(1);
//       if (count($list2mois) == 0) 
//        {
//            $columns = array(
//                new Column\NumberColumn(array('id' => 'id1', 'field' => 'id', 'source' => true, 'primary' => true, 'title' => 'id')),
//                new Column\TextColumn(array('id' => 'prenom', 'field' => 'prenom', 'source' => true, 'title' => 'Prénom')),
//                new Column\TextColumn(array('id' => 'nom', 'field' => 'nom', 'source' => true, 'title' => 'Nom')),
//                new Column\TextColumn(array('id' => 'nummandat', 'field' => 'nummandat', 'source' => true, 'title' => 'Numéro de mandat')),
//                new Column\DateTimeColumn(array('id' => 'datefin', 'field' => 'datefin', 'source' => true, 'title' => 'Date de fin', 'format' => 'd/m/Y')),
//                new Column\TextColumn(array('id' => 'client', 'field' => 'client', 'source' => true, 'title' => 'Client')),
//            );
//
//            $source2 = new Vector(array(), $columns);
//        }
//       else
//       {
//           $source2 = new Vector($list2mois);
//       }
//           $source2->setId(array('id','id1'));
//       
//       
//        $list3mois = $this->getDoctrine()
//        ->getRepository('NurunRhBundle:ConseillerMandat')
//        ->findAffectationsWithDelay(2);
//        if (count($list3mois) == 0) 
//        {
//        $columns = array(
//                new Column\NumberColumn(array('id' => 'id2', 'field' => 'id', 'source' => true, 'primary' => true, 'title' => 'id')),
//                new Column\TextColumn(array('id' => 'prenom', 'field' => 'prenom', 'source' => true, 'title' => 'Prénom')),
//                new Column\TextColumn(array('id' => 'nom', 'field' => 'nom', 'source' => true, 'title' => 'Nom')),
//                new Column\DateTimeColumn(array('id' => 'datefin', 'field' => 'datefin', 'source' => true, 'title' => 'Date de fin', 'format' => 'd/m/Y')),
//                new Column\TextColumn(array('id' => 'nummandat', 'field' => 'nummandat', 'source' => true, 'title' => 'Numéro de mandat')),
//                new Column\TextColumn(array('id' => 'client', 'field' => 'client', 'source' => true, 'title' => 'Client')),
//            );
//
//            $source3 = new Vector(array(), $columns);
//        }
//       else
//       {
//           $source3 = new Vector($list3mois);
//       }
//           $source3->setId(array('id','id2'));
//       
//       
//
//        $grid1mois = $gridManager->createGrid();
//        $grid1mois->setSource($source1);
//        $grid1mois->setNoResultMessage("Pas de conseillers concernés");
//        $grid1mois->setActionsColumnTitle('Action');
//        $rowAction = new RowAction("Voir", 'nurun_affectation_view', false, '_self', array('class' => 'grid_view_action'));
//        if (count($list1mois) != 0) 
//        {
//            var_dump($list1mois);
//            $rowAction->setRouteParametersMapping(array('id0' => 'id'));
//            $grid1mois->addRowAction($rowAction);
//        }
//        $grid1mois->getColumn('id0')
//            ->setFilterable(false)
//            ->setVisible(false)
//            ->setSortable(false);
//        $grid1mois->getColumn('nom')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Nom')
//            ->setSortable(false);
//        $grid1mois->getColumn('prenom')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Prénom')
//            ->setSortable(false);
//        $grid1mois->getColumn('nummandat')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Numéro de mandat')
//            ->setSortable(false);
//        $grid1mois->getColumn('client')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Client')
//            ->setSortable(false);
//        $grid1mois->getColumn('datefin')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Date de fin')
//            ->setSortable(false);        
//        
//        $grid2mois = $gridManager->createGrid();
//        $grid2mois->setSource($source2);
//        $grid2mois->setNoResultMessage("Pas de conseillers concernés");
//        $grid2mois->setActionsColumnTitle('Action');
//        $rowAction = new RowAction("Voir", 'nurun_affectation_view', false, '_self', array('class' => 'grid_view_action'));
//        if (count($list2mois) != 0) 
//        {
//            var_dump($list2mois);
//            $rowAction->setRouteParametersMapping(array('id1' => 'id'));
//            $grid2mois->addRowAction($rowAction); 
//        }
//        
//        $grid2mois->getColumn('id1')
//            ->setFilterable(false)
//            ->setVisible(false)
//            ->setSortable(false);
//        $grid2mois->getColumn('nom')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Nom')
//            ->setSortable(false);
//        $grid2mois->getColumn('prenom')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Prénom')
//            ->setSortable(false);
//        $grid2mois->getColumn('nummandat')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Numéro de mandat')
//            ->setSortable(false);
//        $grid2mois->getColumn('client')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Client')
//            ->setSortable(false);
//        $grid2mois->getColumn('datefin')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Date de fin')
//            ->setSortable(false);
//        
//        $grid3mois = $gridManager->createGrid();
//        $grid3mois->setSource($source3);
//        $grid3mois->setActionsColumnTitle('Action');
//        $rowAction = new RowAction("Voir", 'nurun_affectation_view', false, '_self', array('class' => 'grid_view_action'));
//        if (count($list3mois) != 0) 
//        {
//            var_dump($list3mois);
//            $rowAction->setRouteParametersMapping(array('id2' => 'id'));
//            $grid3mois->addRowAction($rowAction);
//        }
//        $grid3mois->setNoResultMessage("Pas de conseillers concernés");
//        $grid3mois->getColumn('id2')
//            ->setFilterable(false)
//            ->setVisible(false)
//            ->setSortable(false);
//        $grid3mois->getColumn('nom')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Nom')
//            ->setSortable(false);
//        $grid3mois->getColumn('prenom')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Prénom')
//            ->setSortable(false);
//        $grid3mois->getColumn('nummandat')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Numéro de mandat')
//            ->setSortable(false);
//        $grid3mois->getColumn('client')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Client')
//            ->setSortable(false);
//        $grid3mois->getColumn('datefin')
//            ->setFilterable(false)
//            ->setVisible(true)
//            ->setTitle('Date de fin')
//            ->setSortable(false);
//        
//        if ($gridManager->isReadyForRedirect())
//        {
//            if ($gridManager->isReadyForExport())
//        {
//            return $gridManager->getExportResponse();
//        }
//
//    return new RedirectResponse($gridManager->getRouteUrl());
//}
//else
//{
//    return $this->render('NurunRhBundle:ConseillerMandat:index.html.twig', array('grid1' => $grid1mois, 'grid2' => $grid2mois, 'grid3' => $grid3mois));
//}
//       return $this->render('NurunRhBundle:ConseillerMandat:index.html.twig', array('grid1' => $grid1mois, 'grid2' => $grid2mois, 'grid3' => $grid3mois));

//    $listEnIm = $this->getDoctrine()
//        ->getRepository('NurunRhBundle:Conseiller')
//        ->findWithMandatExpired();
//    
//    $listConseillers = $this->getDoctrine()
//        ->getRepository('NurunRhBundle:Conseiller')
//            ->findAll();
//    
//    $listConseillersWithoutMandat = array();
//    
//    foreach($listConseillers as $conseiller)
//    {
//        $conseillerEnMandat = $this->getDoctrine()
//        ->getRepository('NurunRhBundle:ConseillerMandat')
//        ->findByConseiller($conseiller);
//        
//        if (empty($conseillerEnMandat)) 
//        {
//            $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
//            $jsonObject = $serializer->serialize($conseiller, 'json');
//            $jsonArray =  json_decode($jsonObject);
//            
//            $listConseillersWithoutMandat[] = (array)$jsonArray;
//        }   
//    }
////    var_dump($listConseillers);
//    $listSansMandat = array_merge($listEnIm, $listConseillersWithoutMandat);
    