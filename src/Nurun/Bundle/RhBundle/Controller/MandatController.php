<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Writer\DoctrineWriter;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Export\CSVExport;
use APY\DataGridBundle\Grid\Export\ExcelExport;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Column\DateColumn;
use APY\DataGridBundle\Grid\Action\MassAction;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\ConseillerMandat;
use Nurun\Bundle\RhBundle\Entity\Mandat;
use Nurun\Bundle\RhBundle\Entity\Adresse;
use Nurun\Bundle\RhBundle\Entity\Document;
use Nurun\Bundle\RhBundle\Entity\Conseiller;
use Nurun\Bundle\RhBundle\Form\MandatType;
use Nurun\Bundle\RhBundle\Form\AddConseillerMandatsType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;

class MandatController extends Controller
{
  /**
   * @Security("has_role('ROLE_RDP')")
   */
  // Affichage de la liste des conseillers
  public function indexAction(Request $request)
  {
    $viewAll = $request->query->get('viewAll');

    $listMandats = $this->getDoctrine()
      ->getRepository('NurunRhBundle:Mandat')
      ->findAll();

    return $this->render('NurunRhBundle:Mandat:index.html.twig', array(
      'mandats' => $listMandats,
      'viewAll' => $viewAll
    ));
  }


    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function exportMandatsAction(Request $request)
    {

        // On va travailler avec le $repository des mandats
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Mandat');

        // On récupère tous les mandats
        $mandats = $repository->findAll();

        $mandatsArray = array();


        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($mandats as $mandat) {
            if (!($mandat->isDeleted())) {
                $mandatArray = array();
                $mandatArray['client'] = $mandat->getClient()->getAcronyme();
                $mandatArray['titre'] = $mandat->getTitre();
                $mandatArray['identifiant'] = $mandat->getIdentifiant();
                $mandatArray['secteur'] = $mandat->getSecteur();
                if (!empty($mandat->getChargeprojet())) {
                    $mandatArray['CP'] = $mandat->getChargeprojet();
                } else {
                    $mandatArray['CP'] = 'Inconnu';
                }
                if (!empty($mandat->getMandataire())) {
                    $mandatArray['mandataire'] = $mandat->getMandataire();
                } else {
                    $mandatArray['mandataire'] = 'Inconnu';
                }
                if (!empty($repository->findCoordonnateurs($mandat->getId()))) {
                    if (empty($mandatArray['coordonnateurs'])) {
                        $mandatArray['coordonnateurs'] = '';
                    }
                    foreach ($repository->findCoordonnateurs($mandat->getId()) as $coordonnateur) {

                        $mandatArray['coordonnateurs'] .= $coordonnateur->getDisplay() . ' ';
                    }}
                else {
                        $mandatArray['coordonnateurs'] = 'Inconnu';
                    }

                $mandatsArray[] = $mandatArray;
                unset($mandatArray);
            }
        }

        //On trie la liste des conseillers par la date de fin
        foreach ($mandatsArray as $key => $row) {
            $client[$key]  = $row['client'];
        }

        array_multisort($client, SORT_ASC, $mandatsArray);

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Liste des mandats actifs")
            ->setSubject("Liste des mandats")
            ->setDescription("Liste des mandats actifs triés par client")
            ->setKeywords("conseillers kentel affectations mandats")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Mandats de nos conseillers');

        // $em = $this->get('doctrine')->getManager();
        // $user = $this->getUser();

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Client')
            ->setCellValue('B' . $numligne, 'Titre')
            ->setCellValue('C' . $numligne, 'Identifiant')
            ->setCellValue('D' . $numligne, 'Secteur')
            ->setCellValue('E' . $numligne, 'Chef de projet')
            ->setCellValue('F' . $numligne, 'Mandataire')
            ->setCellValue('G' . $numligne, 'Coordonnateurs');

        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('f7f7f7');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1:B600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1:C600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('E1:E600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('F1:F600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('G1:G600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(65);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(5);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(60);

        $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne.':G'.$numligne)->getFont()->setBold(true);
        $numligne++;

        foreach($mandatsArray as $mandat)
        {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $mandat['client']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B' . $numligne, $mandat['titre']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C' . $numligne, $mandat['identifiant']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D' . $numligne, $mandat['secteur']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E' . $numligne, $mandat['CP']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F' . $numligne, $mandat['mandataire']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G' . $numligne, $mandat['coordonnateurs']);


            $numligne++;

        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Mandats.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
   * @Security("has_role('ROLE_GESTION')")
   */
  public function addAction(Request $request)
  {
    if (!$this->get('security.context')->isGranted('ROLE_GESTION')) {
      // Sinon on déclenche une exception « Accès interdit »
      throw new AccessDeniedException('Accès limité aux gestionnaires.');
    }
    // On crée un objet Mandat
    $mandat = new Mandat();
    $user = $this->get('security.context')->getToken()->getUser();
    $mandat->setSecteur($user->getVp());
    
    // On crée le FormBuilder grâce au service form factory
    $form = $this->get('form.factory')->create(new MandatType(), $mandat);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($mandat);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Mandat bien enregistré.');

      return $this->redirect($this->generateUrl('nurun_mandat_view', array('id' => $mandat->getId())));
    }

    return $this->render('NurunRhBundle:Mandat:add.html.twig', array(
      'form' => $form->createView(),
    ));
    
  }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function adressesIndexAction(Mandat $mandat)
    {
        $adresses = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Adresse')
            ->findByMandat($mandat);

        return $this->render('NurunRhBundle:Mandat:indexMandatAdresses.html.twig', array(
            'id'    => $mandat->getId(),
            'adresses'          => $adresses,
        ));

    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function coordonnateursIndexAction(Mandat $mandat)
    {
        $coordonnateurs = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->findByMandatCoordination($mandat);

        return $this->render('NurunRhBundle:Mandat:indexMandatCoordonnateurs.html.twig', array(
            'id'    => $mandat->getId(),
            'coordonnateurs'          => $coordonnateurs,
        ));

    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     * @ParamConverter("mandat", options={"mapping": {"mandatId" : "id"}})
     * @ParamConverter("adresse", options={"mapping": {"adresseId" : "id"}})
     */
    public function adresseDeleteAction(Mandat $mandat, Adresse $adresse)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $mandat->removeAdress($adresse);
        $em->persist($mandat);
        $em->flush();
        $adresses = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Adresse')
            ->findByMandat($mandat);

        return $this->render('NurunRhBundle:Mandat:indexMandatAdresses.html.twig', array(
            'id'    => $mandat->getId(),
            'adresses'          => $adresses,
            'alerte'    => 'Adresse supprimée.',
        ));

    }


    /**
     * @Security("has_role('ROLE_GESTION')")
     * @ParamConverter("mandat", options={"mapping": {"mandatId" : "id"}})
     * @ParamConverter("coordonnateur", options={"mapping": {"coordonnateurId" : "id"}})
     */
    public function coordonnateurDeleteAction(Mandat $mandat, Conseiller $coordonnateur)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $mandat->removeCoordonnateur($coordonnateur);
        $em->persist($mandat);
        $em->flush();
        $coordonnateurs = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->findByMandatCoordination($mandat);

        return $this->render('NurunRhBundle:Mandat:indexMandatCoordonnateurs.html.twig', array(
            'id'    => $mandat->getId(),
            'coordonnateurs'          => $coordonnateurs,
            'alerte'    => 'Coordonnateur enlevé.',
        ));

    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     * @ParamConverter("mandat", options={"mapping": {"mandatId": "id"}})
     */
    public function adressesAddAction(Mandat $mandat)
    {
        $this->denyAccessUnlessGranted('ROLE_GESTION', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $adresses = $em->getRepository('NurunRhBundle:Adresse')->findAll();

        return $this->render('NurunRhBundle:Mandat:addMandatAdresses.html.twig', array(
                'adresses'    => $adresses,
                'mandat'          => $mandat
            )
        );

    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     * @ParamConverter("mandat", options={"mapping": {"mandatId": "id"}})
     */
    public function coordonnateursAddAction(Mandat $mandat)
    {
        $this->denyAccessUnlessGranted('ROLE_GESTION', null,'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $coordonnateurs = $em->getRepository('NurunRhBundle:Conseiller')->findByDeletedAt(null);

        return $this->render('NurunRhBundle:Mandat:addMandatCoordonnateurs.html.twig', array(
                'coordonnateurs'    => $coordonnateurs,
                'mandat'          => $mandat
            )
        );
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     * @ParamConverter("mandat", options={"mapping": {"mandatId": "id"}})
     * @ParamConverter("adresse", options={"mapping": {"adresseId" : "id"}})
     */
    public function adresseAddAction(Mandat $mandat, Adresse $adresse)
    {
        $this->denyAccessUnlessGranted('ROLE_GESTION', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getEntityManager();
        $mandat->addAdress($adresse);
        $em->persist($mandat);
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_mandat_adresses_add', array('mandatId'=> $mandat->getId())));

    }


    /**
     * @Security("has_role('ROLE_GESTION')")
     * @ParamConverter("mandat", options={"mapping": {"mandatId": "id"}})
     * @ParamConverter("coordonnateur", options={"mapping": {"coordonnateurId" : "id"}})
     */
    public function coordonnateurAddAction(Mandat $mandat, Conseiller $coordonnateur)
    {
        $this->denyAccessUnlessGranted('ROLE_GESTION', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getEntityManager();
        $mandat->addCoordonnateur($coordonnateur);
        $em->persist($mandat);
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('nurun_mandat_coordonnateurs_add', array('mandatId'=> $mandat->getId())));

    }

        /**
   * @Security("has_role('ROLE_GESTION')")
   * @ParamConverter("mandat", options={"mapping": {"mandatId": "id"}})
   */
  public function addAffectationsAction(Request $request, Mandat $mandat)
    {
      if($mandat->isDeleted()){
        throw new AccessDeniedException('Impossible de modifier un mandat archivé.');
      }

      $em = $this->getDoctrine()->getManager();

      // On crée un objet Mandat
      $affectation = new ConseillerMandat();

      // On fixe des valeurs par défaut
      $statutAffectation = $em->getRepository('NurunRhBundle:StatutAffectation')->findOneByAcronyme('A');
      $affectation->setStatutAffectation($statutAffectation);
      $affectation->setPourcentage('100');
      $affectation->setMandat($mandat);
      $affectation->setIdentifiantMandat($mandat->getClient());

      //Il nous faut aussi la liste de tous les conseillers
      $conseillerList = $em->getRepository('NurunRhBundle:Conseiller')->findAll();

      // On charge le formulaire
      $form = $this->get('form.factory')->create(new AddConseillerMandatsType(), $affectation);
      $form2 = $this->createFormBuilder()
        ->add('conseiller',  'text', array(
          'required' => false))
        ->getForm();

      // On teste si il a déjà été exécuté correctement
      $form->handleRequest($request);
      $form2->handleRequest($request);

      $conseillerIdString = $form2['conseiller']->getData();
      $conseillerIdArray = explode("(", $conseillerIdString);

      $conseillerIds = array();
      foreach ($conseillerIdArray as $conseillerId) {
        $conseillerFound = strpos($conseillerId, ")");
        if($conseillerFound != false){
          $conseillerIds[] = substr($conseillerId, $conseillerFound-1, 1);
        }
      }

      if($form->isValid() && $form->isSubmitted() && !empty($conseillerIds)){

        $nombreCopy = 0;
        foreach ($conseillerIds as $conseillerId) {
          $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($conseillerId);
          if($nombreCopy == 0){
            $affectation->setConseiller($conseiller);
          }
          else{
            $affectation = clone $affectation;
            $affectation->setConseiller($conseiller);
          }
          $nombreCopy++;
          $em->persist($affectation);
          $em->flush();

          $user = $this->getUser();
          $body = $this->renderView('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectation, 'action' => 'Ajout', 'user' => $user));
          $notification = $this->get('nurun.notification');
          $notification->notify($request->get('_route'), $body, $affectation);

          // On prépare les arguments nécessaire pour la notification courriel
          // On récupère le secteur du conseiller affecté
          if (empty($conseiller->getvicePresidence())) {
            $vp = 'VPTS';
          }
          else {
            $vp = $conseiller->getvicePresidence()->getAcronyme();
          }
          $user = $this->getUser();
          $destinataire = array();
          // Si il s'agit de vacances on ajoute le responsable de suivi des vacances
          if ($affectation->getIdentifiantMandat() == 'NSC-Vacances') {
            $system = $em->getRepository('NurunSystemBundle:System')->findAll();
            $destinataire[] = $system[0]->getEmailGestionVacances();
          } else {
            $destinataire = null;
          }
          $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($conseiller);

          if (!empty($rdp))
          {
              $destinataire[] = $rdp->getRdp()->getCourriel();
          }

          $alert = $this->renderView('NurunRhBundle:ConseillerMandat:email.txt.twig', array('affectation' => $affectation, 'action' => 'Ajout', 'auteur' => $user->getUserName()));

          // Enfin on appelle le service de courriel pour envoyer la notification
          $notify = $this->get('send.email');
          $notify->notifyVp('Ajout d affectation', $alert, $vp, $destinataire, $user);
        }
        $request->getSession()->getFlashBag()->add('notice', 'Affectations bien enregistrées.');

        return $this->redirect($this->generateUrl('nurun_mandat_view', array('id' => $mandat->getId())));
      }
      else if($form->isValid() && $form->isSubmitted() && empty($conseillerIds)){
        $form->addError(new FormError('Veuillez sélectionner au moins 1 conseiller'));
      }

      return $this->render('NurunRhBundle:Mandat:addAffectations.html.twig', array(
        'form'            => $form->createView(),
        'form2'            => $form2->createView(),
        'mandat'          => $mandat,
        'conseillerList'  => $conseillerList
      ));
    }
  
  /**
   * @Security("has_role('ROLE_USER')")
   */
    // Affichage du détail d'un mandat
  public function viewAction($id)
  {
      // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('NurunRhBundle:Mandat')
    ;
    
    // On récupère l'entité correspondante à l'id $id
    $mandat = $repository->find($id);
    
    if (null === $mandat) {
      throw new NotFoundHttpException("Le mandat d'id ".$id." n'existe pas.");
    }

    if($mandat->isDeleted()){
      if (!$this->get('security.context')->isGranted('ROLE_GESTION')) {
        throw new AccessDeniedException('Impossible de voir un mandat archivé.');
      }
    }

    return $this->render(
   'NurunRhBundle:Mandat:view.html.twig', array('mandat' => $mandat));

  }

  /**
   * @Security("has_role('ROLE_ROOT')")
   */
  // Chargement d'un fichier de mandats
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
                $mandat = $em->getRepository('NurunRhBundle:Mandat')
                  ->findOneByIdentifiant($row['identifiant']);
                if (empty($mandat))
                {
                    $mandat = new Mandat();
                    $mandat->setIdentifiant($row['identifiant']);
                }
                
                $client = $em->getRepository('NurunRhBundle:Client')
                  ->findOneByAcronyme($row['client']);
                
                if (!empty($client))
                {
                    $mandat->setClient($client);
                    $mandat->setTitre($row['titre']);
                    $mandat->setType($row['type']);
                    $mandat->setSecteur($row['secteur']);
                    $mandat->setOffre($row['offre']);
                    $mandat->setDateFin(new \DateTime($row['dateFin']));

                    $chargeprojet = $em->getRepository('NurunRhBundle:Conseiller')
                            ->findOneFromText($row['cp'], ' ');
                    if (empty($chargeprojet)) {
                        $logger->error('Une erreur est survenue a la recherche de '.$row['cp']);
                    }
                    else
                    {
                    $mandat->setChargeprojet($chargeprojet);    
                    }
                    
                    $mandataire = $em->getRepository('NurunRhBundle:Conseiller')
                            ->findOneFromText($row['mandataire'], ' ');
                    if (empty($mandataire)) {
                        $logger->error('Une erreur est survenue a la recherche de '.$row['mandataire']);
                    }
                    else
                    {
                    $mandat->setMandataire($mandataire);    
                    }
                $em = $this->getDoctrine()->getManager();
                $em->persist($mandat);
                $em->flush();
                }  
                else
                {
                  $logger->error('Impossible de charger le mandat '.$row['identifiant']);
                }
            }
            $this->redirect($this->generateUrl('nurun_mandat_home'));
        }
    }
        
    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('NurunRhBundle:Mandat:upload.html.twig', array(
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
    $mandat = $em->getRepository('NurunRhBundle:Mandat')->find($id);
    if($mandat->isDeleted()){
      throw new AccessDeniedException('Impossible de modifier un mandat archivé.');
    }
    $form = $this->get('form.factory')->create(new MandatType(), $mandat);
    
    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($mandat);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Mandat bien enregistrée.');

      return $this->redirect($this->generateUrl('nurun_mandat_view', array('id' => $mandat->getId())));
    }

    return $this->render('NurunRhBundle:Mandat:edit.html.twig', array(
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
    $mandat = $em->getRepository('NurunRhBundle:Mandat')->find($id);

    if (null === $mandat) {
      throw new NotFoundHttpException("Le mandat d'id ".$id." n'existe pas.");
    }

    $affectations = $mandat->getConseillers();
    foreach ($affectations as $affectation) {
      if($affectation->isDeleted() == false){
        $em->remove($affectation);
      }
    }
    
    $em->remove($mandat);
    $em->flush();

    $request->getSession()->getFlashBag()->add('info', "Le mandat a bien été supprimée.");

    return $this->redirect($this->generateUrl('nurun_mandat_home'));
  }

  /**
   * @Security("has_role('ROLE_GESTION')")
   */
  public function restoreAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $mandat = $em->getRepository('NurunRhBundle:Mandat')->find($id);

    if (null === $mandat) {
      throw new NotFoundHttpException("Le mandat d'id ".$id." n'existe pas.");
    }

    $mandat->restore();
    $em->flush();

    $request->getSession()->getFlashBag()->add('info', "Le mandat a bien été supprimée.");

    return $this->redirect($this->generateUrl('nurun_mandat_home'));
  }
}


