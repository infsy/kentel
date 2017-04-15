<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\BesoinAffectation;
use Nurun\Bundle\RhBundle\Form\BesoinAffectationType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;

class BesoinAffectationController extends Controller
{

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */

    // Affichage de la liste des besoins

    public function IndexAction()
    {
        $listBesoins = $this->getDoctrine()
            ->getRepository('NurunRhBundle:BesoinAffectation')
            ->findAll();

        return $this->render(
            'NurunRhBundle:BesoinAffectation:index.html.twig', array(
            'besoins' => $listBesoins));
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function AddAction(Request $request)
    {
        // On crée un objet BesoinAffectation
        $besoinAffectation = new BesoinAffectation();

        // On charge le formulaire
        $form = $this->get('form.factory')->create(new BesoinAffectationType(), $besoinAffectation);

        // On teste si il a déjà été exécuté correctement
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($besoinAffectation);
            $em->flush();

            $user = $this->getUser();
            $body = $this->renderView('NurunRhBundle:BesoinAffectation:email.html.twig', array('besoin' => $besoinAffectation, 'action' => 'Ajout', 'user' => $user));
            $notification = $this->get('nurun.notification');
            $notification->notify($request->get('_route'), $body, $besoinAffectation);

            $request->getSession()->getFlashBag()->add('notice', 'Demande bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_affectation_besoin_index'));
        }
        return $this->render('NurunRhBundle:BesoinAffectation:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function DeleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le besoin $id
        $besoin = $em->getRepository('NurunRhBundle:BesoinAffectation')->find($id);

        if (null === $besoin) {
            throw new NotFoundHttpException("Le besoin d'id " . $id . " n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {

            $em->remove($besoin);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "Le besoin a bien été supprimée.");

            return $this->redirect($this->generateUrl('nurun_affectation_besoin_index'));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('NurunRhBundle:BesoinAffectation:delete.html.twig', array(
            'besoin' => $besoin,
            'form' => $form->createView()
        ));
    }


    /**
     * @Security("has_role('ROLE_RDP')")
     */
    // Affichage du détail d'une affectation
    public function viewAction($id)
    {
        // On récupère le repository
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:BesoinAffectation');


        // On récupère la page d'origine de cette requête pour y revenir en cas d'annulation
        $referer = $this->getRequest()->headers->get('referer');

        // On récupère l'entité correspondante à l'id $id
        $besoin = $repository->find($id);

        if (null === $besoin) {
            throw new NotFoundHttpException("Le besoin d'id " . $id . " n'existe pas.");
        }

        return $this->render(
            'NurunRhBundle:BesoinAffectation:view.html.twig', array('besoin' => $besoin,'referer' => $referer));
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le client $id
        $besoin = $em->getRepository('NurunRhBundle:BesoinAffectation')->find($id);
        $form = $this->get('form.factory')->create(new BesoinAffectationType(), $besoin);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($besoin);
            $em->flush();

            $user = $this->getUser();
            $body = $this->renderView('NurunRhBundle:BesoinAffectation:email.html.twig', array('besoin' => $besoin, 'action' => 'Modification', 'user' => $user));
            $notification = $this->get('nurun.notification');
            $notification->notify($request->get('_route'), $body, $besoin);

            $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_affectation_besoin_view', array('id' => $besoin->getId())));
        }

        return $this->render('NurunRhBundle:BesoinAffectation:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function editStatutAction($id, $statut, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le client $id
        $besoin = $em->getRepository('NurunRhBundle:BesoinAffectation')->find($id);

        // On récupère le statut $statut
        $statut = $em->getRepository('NurunRhBundle:StatutAffectation')->find($statut);

        $besoin->setStatutAffectation($statut);

            $em = $this->getDoctrine()->getManager();
            $em->persist($besoin);
            $em->flush();
        return true;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function exportAllAction(Request $request)
    {
        // On va travailler avec le $repository des besoins d'affectation
        $besoinAffectationList = $this->getDoctrine()->getManager()->getRepository('NurunRhBundle:BesoinAffectation')->findAll();

        // on définit le tableau qui va servir a l'export'
        $besoinsAffectationArray = array();

        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($besoinAffectationList as $besoinAffectation) {
            $besoinAffectationArray = array();
            $besoinAffectationArray['id'] = $besoinAffectation->getId();
            $besoinAffectationArray['createdAt'] = $besoinAffectation->getCreatedAt()->format('Y-m-d');
            $besoinAffectationArray['createdBy'] = $besoinAffectation->getCreatedBy();
            $besoinAffectationArray['client'] = $besoinAffectation->getClient();
            $besoinAffectationArray['dateRequise'] = $besoinAffectation->getDateRequise()->format('Y-m-d');
            $besoinAffectationArray['prioriteBesoin'] = $besoinAffectation->getPrioriteBesoin();
            $besoinAffectationArray['profil'] = $besoinAffectation->getProfil();
            $besoinAffectationArray['statutAffectation'] = $besoinAffectation->getStatutAffectation()->getDescription();

            $mandat = $besoinAffectation->getMandat();
            if(!is_null($mandat)){
                $besoinAffectationArray['mandat'] = $mandat;
            }
            else{
                $besoinAffectationArray['mandat'] = "Aucun";
            }

            $sourceAffectation = $besoinAffectation->getSourceAffectation();
            if(!is_null($sourceAffectation)){
                $besoinAffectationArray['sourceAffectation'] = $sourceAffectation;
            }
            else{
                $besoinAffectationArray['sourceAffectation'] = "Aucun";
            }

            $besoinsAffectationArray[] = $besoinAffectationArray;
            unset($besoinAffectationArray);
        }

        $functionSansAccent = function ($chaine) {
            if (version_compare(PHP_VERSION, '5.2.3', '>='))
                $str = htmlentities($chaine, ENT_NOQUOTES, "UTF-8", false);
            else
                $str = htmlentities($chaine, ENT_NOQUOTES, "UTF-8");

            // NB : On ne peut pas utiliser strtr qui fonctionne mal avec utf8.
            $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);

            return $str;
        };

        //On trie la liste par date
        foreach ($besoinsAffectationArray as $key => $row) {
            $date[$key] = $row['createdAt'];
        }
        //On supprime les accents
        $array_sans_accent = array_map($functionSansAccent, $date);
        //On met en minuscule
        $array_lowercase = array_map('strtolower', $array_sans_accent);

        array_multisort($array_lowercase, SORT_DESC, $besoinsAffectationArray);

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Liste des besoins d'affectation")
            ->setSubject("Listing besoins d'affectation")
            ->setDescription("Liste des besoins d'affectation")
            ->setKeywords("'besoin d'affectation' kentel listing")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle("Liste des besoins d'affectation");

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Num')
            ->setCellValue('B' . $numligne, 'Date demande')
            ->setCellValue('C' . $numligne, 'Auteur')
            ->setCellValue('D' . $numligne, 'Client')
            ->setCellValue('E' . $numligne, 'Mandat')
            ->setCellValue('F' . $numligne, 'Date requise')
            ->setCellValue('G' . $numligne, 'Priorité demande')
            ->setCellValue('H' . $numligne, 'Profil')
            ->setCellValue('I' . $numligne, 'Source')
            ->setCellValue('J' . $numligne, 'Statut');

        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('f7f7f7');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1:B600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1:C600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('E1:E600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('F1:F600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('G1:G600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('H1:H600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('I1:I600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('J1:J600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('J')->setWidth(20);

        $phpExcelObject->getActiveSheet()->getStyle('A' . $numligne . ':J' . $numligne)->getFont()->setBold(true);
        $numligne++;

        foreach ($besoinsAffectationArray as $besoinAffectation) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $besoinAffectation['id']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B' . $numligne, $besoinAffectation['createdAt']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C' . $numligne, $besoinAffectation['createdBy']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D' . $numligne, $besoinAffectation['client']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E' . $numligne, $besoinAffectation['mandat']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F' . $numligne, $besoinAffectation['dateRequise']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G' . $numligne, $besoinAffectation['prioriteBesoin']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('H' . $numligne, $besoinAffectation['profil']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I' . $numligne, $besoinAffectation['sourceAffectation']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J' . $numligne, $besoinAffectation['statutAffectation']);


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
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'besoinsAffectation_Nurun.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("besoinAffectation", options={"mapping": {"besoinId": "id"}})
     */
    public function exportAction(Request $request, BesoinAffectation $besoinAffectation)
    {
        $besoinAffectationId = $besoinAffectation->getId();
        //Données du mandat
        $mandat = $besoinAffectation->getMandat();
        if(!is_null($mandat)){
            $besoinAffectationArray['Mandat']['Mandat'] = $mandat;
        }
        else{
            $besoinAffectationArray['Mandat']['Mandat'] = "Aucun";
        }
        $client = $besoinAffectation->getClient();
        if(!is_null($client)){
            $besoinAffectationArray['Mandat']['Client'] = $client;
        }
        else{
            $besoinAffectationArray['Mandat']['Client'] = "Aucun";
        }
        $contexte = $besoinAffectation->getContexte();
        if(!is_null($contexte)){
            $besoinAffectationArray['Mandat']['Contexte'] = $contexte;
        }
        else{
            $besoinAffectationArray['Mandat']['Contexte'] = "Non précisé";
        }
        $besoinAffectationArray['Mandat']['Date de début'] = $besoinAffectation->getDateDebut()->format('Y-m-d');
        $besoinAffectationArray['Mandat']['Date de fin'] = $besoinAffectation->getDateFin()->format('Y-m-d');
        $besoinAffectationArray['Mandat']['Date requise'] = $besoinAffectation->getDateRequise()->format('Y-m-d');
        $prioriteBesoin = $besoinAffectation->getPrioriteBesoin();
        $besoinAffectationArray['Mandat']['Priorité du besoin'] = $prioriteBesoin;

        //Données du profil
        $profil = $besoinAffectation->getProfil()->getProfil();
        $besoinAffectationArray['Profil']['Profil'] = $profil;
        $besoinAffectationArray['Profil']['Niveau de compétence'] = $besoinAffectation->getNiveauCompetence()->getNiveau();
        $besoinAffectationArray['Profil']['Niveau de mobilité'] = $besoinAffectation->getNiveauMobilite()->getNiveau();
        $besoinAffectationArray['Profil']['Niveau de langue'] = $besoinAffectation->getNiveauLangue()->getNiveau();
        $propositionAffectation = $besoinAffectation->getPropositionAffectation();
        if(!is_null($propositionAffectation)){
            $besoinAffectationArray['Profil']['Conseiller proposé'] = $propositionAffectation;
        }
        else{
            $besoinAffectationArray['Profil']['Conseiller proposé'] = "Pas de conseiller proposé";
        }
        $sourceAffectation = $besoinAffectation->getSourceAffectation();
        if(!is_null($sourceAffectation)){
            $besoinAffectationArray['Profil']['sourceAffectation'] = $sourceAffectation;
        }
        else{
            $besoinAffectationArray['Profil']['sourceAffectation'] = "Source non précisée";
        }

        //Données financières
        $budget = $besoinAffectation->getBudget();
        if(!is_null($budget)){
            $besoinAffectationArray['Paramètres financiers']['Budget'] = $budget;
        }
        else{
            $besoinAffectationArray['Paramètres financiers']['Budget'] = "Aucun";
        }
        $penalite = $besoinAffectation->getPenalite();
        if(!is_null($penalite)){
            if($penalite){
                $besoinAffectationArray['Paramètres financiers']['Pénalité'] = 'Oui';
            }
            else{
                $besoinAffectationArray['Paramètres financiers']['Pénalité'] = 'Non';
            }
            
        }
        else{
            $besoinAffectationArray['Paramètres financiers']['Pénalité'] = "Inconnu";
        }

        //Commentaire
        $commentaire = $besoinAffectation->getCommentaire();
        if(!is_null($commentaire)){
            $besoinAffectationArray['Commentaire']['Commentaire'] = $commentaire;
        }
        else{
            $besoinAffectationArray['Commentaire']['Commentaire'] = "Non précisé";
        }

        //Auteur
        if (!is_null($besoinAffectation->getCreatedBy()))
        {
        $besoinAffectationArray['Auteur']['Auteur'] = $besoinAffectation->getCreatedBy()->getUsername();
        }
        else
        {
            $besoinAffectationArray['Auteur']['Auteur'] = 'inconnu';
        }
        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Détail du besoin d'affectation ".$besoinAffectationId)
            ->setSubject("Détail du besoin d'affectation ".$besoinAffectationId)
            ->setDescription("Détail du besoin d'affectation ".$besoinAffectationId)
            ->setKeywords("'besoin d'affectation' kentel détail")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle("Détail du besoin d'affectation");

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, $besoinAffectationId.' - '.$client.' - '.$mandat.' - '.$profil.' - '.$prioriteBesoin);

        $phpExcelObject->getActiveSheet()->mergeCells('A1:B1');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(60);

        $phpExcelObject->getActiveSheet()->getStyle('A' . $numligne . ':B' . $numligne)->getFont()->setBold(true);
        $numligne = $numligne + 2;

        foreach ($besoinAffectationArray as $section => $attributes) {
            $phpExcelObject->getActiveSheet()->getStyle('A' . $numligne)->getFont()->setBold(true);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$numligne, $section);
            $numligne++;

            foreach ($attributes as $attribute => $value) {
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$numligne, $attribute);
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$numligne, $value);

                $phpExcelObject->getActiveSheet()->getStyle('B'.$numligne)->getAlignment()->setWrapText(true);
                $numligne++;
            }
            $numligne++;
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->getStyle('A1:' . $phpExcelObject->getActiveSheet()->getHighestDataColumn() . $phpExcelObject->getActiveSheet()->getHighestDataRow() )->getBorders()->getAllBorders()->setBorderStyle('thin');

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'besoinsAffectation'.$besoinAffectationId.'_Nurun.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}