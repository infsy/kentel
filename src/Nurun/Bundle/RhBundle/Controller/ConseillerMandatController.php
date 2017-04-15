<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Nurun\Bundle\RhBundle\Entity\ConseillerMandat;
use Nurun\Bundle\RhBundle\Form\ConseillerMandatType;
use Nurun\Bundle\RhBundle\Form\ConseillerMandatEditType;
use Nurun\Bundle\RhBundle\Form\ConseillerMandatCongeType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Symfony\Component\Validator\Constraints\Null;
use Symfony\Component\Form\FormError;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;

class ConseillerMandatController extends Controller
{

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */

    // Affichage de la liste des affectations

    public function indexAction(Request $request)
    {
        $viewAll = $request->query->get('viewAll');

        $listAffectations = $this->getDoctrine()
            ->getRepository('NurunRhBundle:ConseillerMandat')
            ->findAll();

        return $this->render(
            'NurunRhBundle:ConseillerMandat:index.html.twig', array(
            'affectations' => $listAffectations,
            'viewAll' => $viewAll
        ));

    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     */

    // Affichage de la liste des affectations par secteur

    public function listByVpAction(Request $request,$vp)
    {
        $viewAll = $request->query->get('viewAll');

        $listAffectations = $this->getDoctrine()
            ->getRepository('NurunRhBundle:ConseillerMandat')
            ->findByVp($vp);

        return $this->render(
            'NurunRhBundle:ConseillerMandat:index.html.twig', array(
            'affectations' => $listAffectations,
            'viewAll' => $viewAll
        ));

    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function exportAffectationsAction(Request $request)
    {

        // On va travailler avec le $repository des conseillers
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:ConseillerMandat');

        // On récupère les conseillers ayant leur fête dans l'intervalle de date'
        $affectations = $repository->findAll();

        $affectationsArray = array();


        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($affectations as $affectation) {
            if (!($affectation->isDeleted())) {
                $affectationArray = array();
                $affectationArray['id'] = $affectation->getConseiller()->getNumEmploye();
                $affectationArray['prenom'] = $affectation->getConseiller()->getPrenom();
                $affectationArray['nom'] = $affectation->getConseiller()->getNom();
                if (!empty($affectation->getConseiller()->getProfil())) {
                    $affectationArray['profil'] = $affectation->getConseiller()->getProfil()->getProfil();
                } else {
                    $affectationArray['profil'] = 'non défini';
                }
                $affectationArray['client'] = $affectation->getMandat()->getClient()->getAcronyme();
                $affectationArray['nummandat'] = $affectation->getMandat()->getIdentifiant();
                $affectationArray['datedeb'] = $affectation->getDateDebut()->format('Y-m-d');
                $affectationArray['datefin'] = $affectation->getDateFin()->format('Y-m-d');
                $affectationArray['pourcentage'] = $affectation->getPourcentage();
                if (!empty($affectation->getConseiller()->getVicePresidence())) {
                    $affectationArray['secteur'] = $affectation->getConseiller()->getVicePresidence()->getAcronyme();
                } else {
                    $affectationArray['secteur'] = 'non défini';
                }
                $affectationsArray[] = $affectationArray;
                unset($affectationArray);
            }
        }

        //On trie la liste des conseillers par la date de fin
        foreach ($affectationsArray as $key => $row) {
            $datefin[$key]  = $row['datefin'];
        }

        array_multisort($datefin, SORT_ASC, $affectationsArray);

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Liste des affectations de nos conseillers")
            ->setSubject("Liste des affectations")
            ->setDescription("Liste des affectations triées par date de fin")
            ->setKeywords("conseillers kentel affectations mandats")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Affectations de nos conseillers');

        // $em = $this->get('doctrine')->getManager();
        // $user = $this->getUser();

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Num employé')
            ->setCellValue('B' . $numligne, 'Prénom')
            ->setCellValue('C' . $numligne, 'Nom')
            ->setCellValue('D' . $numligne, 'Profil')
            ->setCellValue('E' . $numligne, 'Client')
            ->setCellValue('F' . $numligne, 'Num Mandat')
            ->setCellValue('G' . $numligne, 'Date déb')
            ->setCellValue('H' . $numligne, 'Date fin')
            ->setCellValue('I' . $numligne, '%')
            ->setCellValue('J' . $numligne, 'Secteur');

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
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(45);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('I')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('J')->setWidth(20);

        $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne.':J'.$numligne)->getFont()->setBold(true);
        $numligne++;

        foreach($affectationsArray as $affectation)
        {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $affectation['id']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B' . $numligne, $affectation['prenom']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C' . $numligne, $affectation['nom']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D' . $numligne, $affectation['profil']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E' . $numligne, $affectation['client']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F' . $numligne, $affectation['nummandat']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G' . $numligne, $affectation['datedeb']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('H' . $numligne, $affectation['datefin']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I' . $numligne, $affectation['pourcentage']);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J' . $numligne, $affectation['secteur']);

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
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Affectations_Nurun.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    // fonction pour obtenir la liste des conseillers disponibles sur une période donnée

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function chooseVpDatesAction($target, Request $request)
    {
        $user = $this->getUser();

        $defaults = array(
            'dateDeb' => new \DateTime(),
            'dateFin' => new \DateTime("now +3 months"),
            'Secteur' => $user->getVp()
        );

        $form = $this->createFormBuilder($defaults)
            ->add('dateDeb', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('dateFin', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('Secteur', 'choice', array(
                'choices' => array('VPAS' => 'VPAS', 'VPTS' => 'VPTS', 'VPSI' => 'VPSI', 'TOUT' => 'Tout'),
                'required' => true))
            ->add('pigiste', 'checkbox', array(
                'required' => false))
            ->add('affichage', 'choice', array(
                'choices' => array('bench' => 'Conseillers en disponibilités complètes', 'notFullyAffected' => 'Conseillers en disponibilités partielles'),
                'required' => true,
                'preferred_choices' => 'bench',
                'placeholder' => false))
            ->add('Soumettre', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $interval = $data['dateFin']->diff($data['dateDeb']);
            if ($interval->m >= 4) {
                $form->get('dateFin')->addError(new FormError('Écart de plus de 4 mois'));
                return $this->render('NurunRhBundle:ConseillerMandat:choiceDates.html.twig', array(
                    'form' => $form->createView(),
                ));
            }

            return $this->redirect($this->generateUrl($target, array(
                'vp' => $data['Secteur'],
                'dateDeb' => $data['dateDeb']->format('Y-M-d'),
                'dateFin' => $data['dateFin']->format('Y-M-d'),
                'includePigiste' => $data['pigiste'],
                'affichage' => $data['affichage']
            ))
            );
        }
        return $this->render('NurunRhBundle:ConseillerMandat:choiceDates.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function executiveByVpAction(Request $request, $vp, $dateDeb, $dateFin)
    {
        $includePigiste = $request->query->get('includePigiste');
        $affichage = $request->query->get('affichage');
        if ($affichage == "notFullyAffected") {
            $notFullyAffected = true;
        } else {
            $notFullyAffected = false;
        }

        $listConseillersPeriod = $this->prepareExecutive($vp, $dateDeb, $dateFin, $includePigiste, $notFullyAffected);

        return $this->render('NurunRhBundle:ConseillerMandat:executive.html.twig', array(
                'listConseillersPeriod' => $listConseillersPeriod,
                'vp' => $vp,
                'dateDeb' => $dateDeb,
                'dateFin' => $dateFin
            )
        );

    }

    //    fonction pour la gestion du banc

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    private function prepareExecutive($vp, $dateDeb, $dateFin, $includePigiste = null, $notFullyAffected = null)
    {
        $listConseillers = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->listRegardingVp($vp);

        // On prépare la liste des mois en français afin de convertir le retour de la commande format
        $moisfr = array('January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars', 'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
            'July' => 'Juillet', 'August' => 'Aout', 'September' => 'Septembre', 'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre');

        $mois = new \DateInterval('P1M');
        $day = new \DateInterval('P1D');

        $dateDeb = new \DateTime($dateDeb);
        $dateFin = new \DateTime($dateFin);
        if ($dateDeb->format('m') == $dateFin->format('m')) {
            $listConseillersPeriod[$moisfr[$dateDeb->format('F')]] = $this->getAvailableConseillers($listConseillers, $dateDeb, $dateFin, $includePigiste, $notFullyAffected);
        } else {
            $dateDebMois = new \DateTime($dateDeb->format('Y-m') . '-01');
            $tmpDate = new \DateTime($dateDebMois->format('Y-m-d'));
            $dateFinMois = $tmpDate->add($mois)->sub($day);
            // echo "debut ".$dateDeb->format('Y-m-d').'<br>';
            // echo "fin ".$dateFinMois->format('Y-m-d').'<br><br>';
            $listConseillersPeriod[$moisfr[$dateDebMois->format('F')]] = $this->getAvailableConseillers($listConseillers, $dateDeb, $dateFinMois, $includePigiste, $notFullyAffected);

            $tmpDate->add($day);
            while ($tmpDate->format('m') != $dateFin->format('m')) {
                $dateDebMois = $tmpDate;
                $tmpDate = new \DateTime($dateDebMois->format('Y-m-d'));
                $dateFinMois = $tmpDate->add($mois)->sub($day);
                // echo "debut ".$dateDebMois->format('Y-m-d').'<br>';
                // echo "fin ".$dateFinMois->format('Y-m-d').'<br><br>';
                $listConseillersPeriod[$moisfr[$dateDebMois->format('F')]] = $this->getAvailableConseillers($listConseillers, $dateDebMois, $dateFinMois, $includePigiste, $notFullyAffected);
                $tmpDate->add($day);
            }
            $dateDebMois = $tmpDate;
            // echo "debut ".$dateDebMois->format('Y-m-d').'<br>';
            // echo "fin ".$dateFin->format('Y-m-d').'<br><br>';
            $listConseillersPeriod[$moisfr[$dateDebMois->format('F')]] = $this->getAvailableConseillers($listConseillers, $dateDebMois, $dateFin, $includePigiste, $notFullyAffected);
        }
        return ($listConseillersPeriod);
    }


    //    fonction pour la gestion du banc

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    private function getAvailableConseillers($listConseillers, $dateDeb, $dateFin, $includePigiste = null, $notFullyAffected = null)
    {
        // On prépare les variables qui vont nous servir : l'appel au service grid et le tableau qu'on va charger dans le grid
        $conseillersDisponibles = array();
        foreach ($listConseillers as $conseiller) {
            if ($notFullyAffected) {
                $listAffectations = $this->getDoctrine()
                    ->getRepository('NurunRhBundle:ConseillerMandat')
                    ->findAffectationsFromDate($conseiller, $dateDeb, $dateFin);
                if (count($listAffectations) > 0) {

                    // Pour chaque conseiller sur le banc, on récupère son nom ainsi que ses propos
                    $conseillerArray = array();
                    $conseillerArray['id'] = $conseiller->getId();
                    $conseillerArray['prenom'] = $conseiller->getPrenom();
                    $conseillerArray['nom'] = $conseiller->getNom();

                    if (empty($conseiller->getProfil())) {
                        $conseillerArray['profil'] = 'Pas de profil';
                    } else {
                        $conseillerArray['profil'] = $conseiller->getProfil()->getProfil();
                    }

                    if (empty($conseiller->getProfil())) {
                        $conseillerArray['profilEN'] = 'Pas de profil';
                    } else {
                        $conseillerArray['profilEN'] = $conseiller->getProfil()->getProfilEN();
                    }
                    if (!empty($conseiller->getStatut())) {
                        if ($conseiller->getStatut()->getDescription() == "Pigiste") {
                            if ($includePigiste) {
                                $conseillerArray['profil'] = 'PIGISTE';
                            } else {
                                continue;
                            }
                        }
                    }

                    // On ajoute le pourcentage d'affectation
                    foreach ($listAffectations as $affectation) {
                        $pourcentage = $affectation->getPourcentage();
                        if (!isset($conseillerPourcentage)) {
                            $conseillerPourcentage = $pourcentage;
                        } else {
                            $conseillerPourcentage += $pourcentage;
                        }
                    }
                    if ($conseillerPourcentage < 100) {
                        $conseillerArray['pourcentage'] = $conseillerPourcentage;
                        $conseillersDisponibles[] = $conseillerArray;
                        unset($conseillerArray, $conseillerPourcentage);
                    }
                }
            } else {
                // Recherche pour chaque conseiller si il a des affectations actives couvrant toute la période en cours
                $listAffectationsActives = $this->getDoctrine()
                    ->getRepository('NurunRhBundle:ConseillerMandat')
                    ->findAffectationsActivesFromDate($conseiller, $dateDeb, $dateFin);

                // Recherche pour chaque conseiller si il a des vides dans la période en cours
                if (count($listAffectationsActives) == 0) {
                    $listVides = $this->getDoctrine()
                        ->getRepository('NurunRhBundle:ConseillerMandat')
                        ->findVidesFromDate($conseiller, $dateDeb, $dateFin);
                } else {
                    continue;
                }
                // Si il a pas d'affectations sur toute la durée alors on peut le placer dans le tableau intermandat
                if ((count($listAffectationsActives) == 0) and ($listVides > 0)) {
                    // Pour chaque conseiller sur le banc, on récupère son nom ainsi que ses propos et affectations a venir
                    $conseillerArray = array();
                    $conseillerArray['id'] = $conseiller->getId();
                    $conseillerArray['prenom'] = $conseiller->getPrenom();
                    $conseillerArray['nom'] = $conseiller->getNom();

                    if (empty($conseiller->getProfil())) {
                        $conseillerArray['profil'] = 'Pas de profil';
                    } else {
                        $conseillerArray['profil'] = $conseiller->getProfil()->getProfil();
                    }
                    if (empty($conseiller->getProfil())) {
                        $conseillerArray['profilEN'] = 'Pas de profil';
                    } else {
                        $conseillerArray['profilEN'] = $conseiller->getProfil()->getProfilEN();
                    }
                    if (!empty($conseiller->getStatut())) {
                        if ($conseiller->getStatut()->getDescription() == "Pigiste") {
                            if ($includePigiste) {
                                $conseillerArray['profil'] = 'PIGISTE';
                            } else {
                                continue;
                            }
                        }
                    }

                    // On récupère sa date de disponibilité
                    $listFinAffectations = $this->getDoctrine()
                        ->getRepository('NurunRhBundle:ConseillerMandat')
                        ->findDatesDispo($conseiller, $dateDeb, $dateFin);
                    $conseillerArray['finaffectations'] = $listFinAffectations;

                    // On récupère ses propositions
                    $resumePropositions = $this->getDoctrine()
                        ->getRepository('NurunRhBundle:ConseillerMandat')
                        ->findPropositionsString($conseiller);

                    // On récupère ses affectations à venir
                    $resumeAffectations = $this->getDoctrine()
                        ->getRepository('NurunRhBundle:ConseillerMandat')
                        ->findFutureAffectationsString($conseiller, $dateDeb);

                    // On concatène les 2
                    if (empty($resumePropositions)) {
                        $resume = $resumeAffectations;
                    } else if (empty($resumeAffectations)) {
                        $resume = $resumePropositions;
                    } else {
                        $resume = $resumeAffectations . ', ' . $resumePropositions;
                    }

                    $conseillerArray['propositions'] = $resume;

                    // Et on place l'ensemble de la description du conseiller sur le banc dans un tableau
                    $conseillersDisponibles[] = $conseillerArray;
                    unset($conseillerArray);
                }
            }
        }
        return $conseillersDisponibles;
    }


    //    fonction pour l'export XLS du banc

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function executiveExportAction(Request $request, $vp, $dateDeb, $dateFin)
    {
        $includePigiste = $request->query->get('includePigiste');
        $executiveData = $this->prepareExecutive($vp, $dateDeb, $dateFin, $includePigiste);

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Tableau du banc")
            ->setSubject("Banc")
            ->setDescription("Gestion du banc")
            ->setKeywords("conseillers kentel affectations banc")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Gestion du banc');
        $phpExcelObject->getActiveSheet()->getStyle('A1:A600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1:B600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1:C600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setWrapText(true);
        $phpExcelObject->getActiveSheet()->getStyle('E1:E600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('F1:F600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(45);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(40);

        $numligne = 1;
        $activeSheet = 1;
        foreach ($executiveData as $mois => $listConseillers) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$numligne.':B'.$numligne)
                ->setCellValue('A' . $numligne, 'Conseillers arrivant sur le banc au mois de '.$mois);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne)->getFont()->setBold(true);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne)->getFont()->setSize(14);

            //Pour un onglet par mois
            $phpExcelObject->createSheet($activeSheet);
            $phpExcelObject->setActiveSheetIndex($activeSheet)->setTitle($mois);
            $phpExcelObject->getActiveSheet()->getStyle('A1:A600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpExcelObject->getActiveSheet()->getStyle('B1:B600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpExcelObject->getActiveSheet()->getStyle('C1:C600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setWrapText(true);
            $phpExcelObject->getActiveSheet()->getStyle('E1:E600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpExcelObject->getActiveSheet()->getStyle('F1:F600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(25);
            $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(45);
            $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(40);
            $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(40);

            $numligneMois = 1;
            $phpExcelObject->getActiveSheet()
                ->mergeCells('A'.$numligneMois.':B'.$numligneMois)
                ->setCellValue('A' . $numligneMois, 'Conseillers arrivant sur le banc au mois de '.$mois);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligneMois)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligneMois)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligneMois)->getFont()->setBold(true);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$numligneMois)->getFont()->setSize(14);

            $numligneMois++;
            $numligne++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, 'Prénom')
                ->setCellValue('B' . $numligne, 'Nom')
                ->setCellValue('C' . $numligne, 'Date Disp')
                ->setCellValue('D' . $numligne, 'Propositions futures')
                ->setCellValue('E' . $numligne, 'Profil')
                ->setCellValue('F' . $numligne, 'Profil (EN)')
                ->getStyle('A' . $numligne . ':F' . $numligne)->getFont()->setBold(true);

            $phpExcelObject->setActiveSheetIndex($activeSheet)
                ->setCellValue('A' . $numligneMois, 'Prénom')
                ->setCellValue('B' . $numligneMois, 'Nom')
                ->setCellValue('C' . $numligneMois, 'Date Disp')
                ->setCellValue('D' . $numligneMois, 'Propositions futures')
                ->setCellValue('E' . $numligneMois, 'Profil')
                ->setCellValue('F' . $numligneMois, 'Profil (EN)')
                ->getStyle('A' . $numligneMois . ':F' . $numligneMois)->getFont()->setBold(true);
            
            $numligne++;
            $numligneMois++;

            foreach($listConseillers as $conseiller) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A' . $numligne, $conseiller['prenom'])
                    ->setCellValue('B' . $numligne, $conseiller['nom'])
                    ->setCellValue('C' . $numligne, $conseiller['finaffectations'])
                    ->setCellValue('D' . $numligne, strip_tags($conseiller['propositions']))
                    ->setCellValue('E' . $numligne, $conseiller['profil'])
                    ->setCellValue('F' . $numligne, $conseiller['profilEN']);

                $phpExcelObject->setActiveSheetIndex($activeSheet)
                    ->setCellValue('A' . $numligneMois, $conseiller['prenom'])
                    ->setCellValue('B' . $numligneMois, $conseiller['nom'])
                    ->setCellValue('C' . $numligneMois, $conseiller['finaffectations'])
                    ->setCellValue('D' . $numligneMois, strip_tags($conseiller['propositions']))
                    ->setCellValue('E' . $numligneMois, $conseiller['profil'])
                    ->setCellValue('F' . $numligneMois, $conseiller['profilEN']);

                $numligneMois++;
                $numligne++;
            }
            $numligne++;
            $activeSheet++;
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Gestion banc.xls'
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
    public function addAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        // On crée un objet Mandat
        $affectation = new ConseillerMandat();

        // On vérifie si on doit afficher le formulaire déjà positionné sur un conseiller
        if ($id != 0) {
            // On récupère le client $id
            $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);
            $affectation->setConseiller($conseiller);
        }

        // On fixe des valeurs par défaut
        $statutAffectation = $em->getRepository('NurunRhBundle:StatutAffectation')->findOneByAcronyme('A');
        $affectation->setStatutAffectation($statutAffectation);
        $affectation->setPourcentage('100');

        // On charge le formulaire
        $form = $this->get('form.factory')->create(new ConseillerMandatType(), $affectation);

        // On teste si il a déjà été exécuté correctement
        $form->handleRequest($request);
    
        if($form->isValid() && $form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($affectation);
            $em->flush();

            $user = $this->getUser();
            $body = $this->renderView('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectation, 'action' => 'Ajout', 'user' => $user));
            $notification = $this->get('nurun.notification');
            $notification->notify($request->get('_route'), $body, $affectation);
            

            // On prépare les arguments nécessaire pour la notification courriel
            // On récupère le secteur du conseiller affecté
            if (empty($affectation->getConseiller()->getvicePresidence())) {
                $vp = 'VPTS';
            }
            else {
            $vp = $affectation->getConseiller()->getvicePresidence()->getAcronyme();
            }
            $destinataire = array();
            // Si il s'agit de vacances on ajoute le responsable de suivi des vacances
            if ($affectation->getIdentifiantMandat() == 'NSC-Vacances') {
                $system = $em->getRepository('NurunSystemBundle:System')->findAll();
                $destinataire[] = $system[0]->getEmailGestionVacances();
            } else {
                $destinataire = null;
            }
            $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($affectation->getConseiller());

            if (!empty($rdp))
            {
                $destinataire[] = $rdp->getRdp()->getCourriel();
            }
            $alert = $this->renderView('NurunRhBundle:ConseillerMandat:email.txt.twig', array('affectation' => $affectation, 'action' => 'Ajout', 'auteur' => $user->getUserName()));

            // Enfin on appelle le service de courriel pour envoyer la notification
            $notify = $this->get('send.email');
            $notify->notifyVp('Ajout d affectation', $alert, $vp, $destinataire, $user);

            $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_affectation_view', array('id' => $affectation->getId())));
        }

        return $this->render('NurunRhBundle:ConseillerMandat:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    // Affichage du détail d'une affectation
    public function viewAction($id, Request $request)
    {
        // On récupère le repository
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:ConseillerMandat');        

        // On récupère l'entité correspondante à l'id $id
        $affectation = $repository->find($id);

        if (null === $affectation) {
            throw new NotFoundHttpException("L'affectation d'id " . $id . " n'existe pas.");
        }

        if($affectation->isDeleted()){
            if (!$this->get('security.context')->isGranted('ROLE_GESTION')) {
                throw new AccessDeniedException('Impossible de voir une affectation archivée.');
            }
        }

        $propositions = $repository->findPropositions($affectation->getConseiller());
        $otherAffectations = $repository->findOtherAffectations($affectation->getConseiller(), $id);

        $session = $request->getSession();
        $session->set('origine', $request->getUri());

        return $this->render(
            'NurunRhBundle:ConseillerMandat:view.html.twig', array('affectation' => $affectation, 'propositions' => $propositions, 'otherAffectations' => $otherAffectations));
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le client $id
        $affectation = $em->getRepository('NurunRhBundle:ConseillerMandat')->find($id);
        if (null === $affectation) {
            throw new NotFoundHttpException("L'affectation d'id " . $id . " n'existe pas.");
        }
        if($affectation->isDeleted()){
            throw new AccessDeniedException('Impossible de modifier une affectation archivée.');
        }
        $form = $this->get('form.factory')->create(new ConseillerMandatEditType(), $affectation);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($affectation);
            $em->flush();

            $user = $this->getUser();
            $body = $this->renderView('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectation, 'action' => 'Modification', 'user' => $user));
            $notification = $this->get('nurun.notification');
            $notification->notify($request->get('_route'), $body, $affectation);

            // On prépare les arguments nécessaire pour la notification courriel
            // On récupère le secteur du conseiller affecté
            $vp = $affectation->getConseiller()->getvicePresidence()->getAcronyme();
            $user = $this->getUser();
            $destinataire = array();

            // Si il s'agit de vacances on ajoute le responsable de suivi des vacances
            if ($affectation->getIdentifiantMandat() == 'NSC-Vacances') {
                $system = $em->getRepository('NurunSystemBundle:System')->findAll();
                $destinataire[] = $system[0]->getEmailGestionVacances();
            } else {
                $destinataire = null;
            }
            $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($affectation->getConseiller());

            if (!empty($rdp))
            {
                $destinataire[] = $rdp->getRdp()->getCourriel();
            }
            $alert = $this->renderView('NurunRhBundle:ConseillerMandat:email.txt.twig', array('affectation' => $affectation, 'action' => 'Mise à jour', 'auteur' => $user->getUserName()));

            // Enfin on appelle le service de courriel pour envoyer la notification
            $notify = $this->get('send.email');
            $notify->notifyVp('Modification d affectation', $alert, $vp, $destinataire, $user);

            $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_affectation_view', array('id' => $affectation->getId())));
        }

        return $this->render('NurunRhBundle:ConseillerMandat:edit.html.twig', array(
            'form'          => $form->createView(),
            'affectation'   => $affectation
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editcongeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le client $id
        $affectation = $em->getRepository('NurunRhBundle:ConseillerMandat')->find($id);
        if($affectation->isDeleted()){
            throw new AccessDeniedException('Impossible de modifier une affectation archivée.');
        }

        if ($affectation->getIdentifiantMandat() != "NSC-Vacances") {
            throw new NotFoundHttpException("Action non autorisée");
        }

        $form = $this->get('form.factory')->create(new ConseillerMandatCongeType(), $affectation);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($affectation);
            $em->flush();

            $user = $this->getUser();
            $body = $this->renderView('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectation, 'action' => 'Modification', 'user' => $user));
            $notification = $this->get('nurun.notification');
            $notification->notify($request->get('_route'), $body, $affectation);

            // On prépare les arguments nécessaire pour la notification courriel
            // On récupère le secteur du conseiller affecté
            $vp = $affectation->getConseiller()->getvicePresidence()->getAcronyme();
            $user = $this->getUser();
            $destinataire = array();

            // Si il s'agit de vacances on ajoute le responsable de suivi des vacances
            if ($affectation->getIdentifiantMandat() == 'NSC-Vacances') {
                $system = $em->getRepository('NurunSystemBundle:System')->findAll();
                $destinataire[] = $system[0]->getEmailGestionVacances();
            } else {
                $destinataire = null;
            }
            $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($affectation->getConseiller());

            if (!empty($rdp))
            {
                $destinataire[] = $rdp->getRdp()->getCourriel();
            }
            $alert = $this->renderView('NurunRhBundle:ConseillerMandat:email.txt.twig', array('affectation' => $affectation, 'action' => 'Mise à jour', 'auteur' => $user->getUserName()));

            // Enfin on appelle le service de courriel pour envoyer la notification
            $notify = $this->get('send.email');
            $notify->notifyVp('Modification affectation', $alert, $vp, $destinataire, $user);

            $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_affectation_view', array('id' => $affectation->getId())));
        }

        return $this->render('NurunRhBundle:Conseiller:editmandatconge.html.twig', array(
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
        $affectation = $em->getRepository('NurunRhBundle:ConseillerMandat')->find($id);

        if (null === $affectation) {
            throw new NotFoundHttpException("L'affectation d'id " . $id . " n'existe pas.");
        }


        $em->remove($affectation);
        $em->flush();

        $user = $this->getUser();
        $body = $this->renderView('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectation, 'action' => 'Suppression', 'user' => $user));
        $notification = $this->get('nurun.notification');
        $notification->notify($request->get('_route'), $body, $affectation);

        // On prépare les arguments nécessaire pour la notification courriel
        // On récupère le secteur du conseiller affecté
        $vp = $affectation->getConseiller()->getvicePresidence()->getAcronyme();
        $user = $this->getUser();
        $destinataire = array();

        // Si il s'agit de vacances on ajoute le responsable de suivi des vacances
        if ($affectation->getIdentifiantMandat() == 'NSC-Vacances') {
            $system = $em->getRepository('NurunSystemBundle:System')->findAll();
            $destinataire[] = $system[0]->getEmailGestionVacances();
        } else {
            $destinataire = null;
        }
        // $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($affectation->getConseiller());

        // if (!empty($rdp))
        // {
        //     $destinataire[] = $rdp->getRdp()->getCourriel();
        // }

        $alert = $this->renderView('NurunRhBundle:ConseillerMandat:email.txt.twig', array('affectation' => $affectation, 'action' => 'Suppression', 'auteur' => $user->getUserName()));

        // Enfin on appelle le service de courriel pour envoyer la notification
        $notify = $this->get('send.email');
        $notify->notifyVp('Suppression d affectation', $alert, $vp, $destinataire, $user);

        $request->getSession()->getFlashBag()->add('info', "L'affectation a bien été supprimée.");

        return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $affectation->getConseiller()->getId()) ));
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function restoreAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $affectation = $em->getRepository('NurunRhBundle:ConseillerMandat')->find($id);

        if (null === $affectation) {
            throw new NotFoundHttpException("L'affectation d'id " . $id . " n'existe pas.");
        }

        $affectation->restore();
        $em->flush();

        $user = $this->getUser();
        $body = $this->renderView('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectation, 'action' => 'Restauration', 'user' => $user));
        $notification = $this->get('nurun.notification');
        $notification->notify($request->get('_route'), $body, $affectation);

        // On prépare les arguments nécessaire pour la notification courriel
        // On récupère le secteur du conseiller affecté
        $vp = $affectation->getConseiller()->getvicePresidence()->getAcronyme();
        $user = $this->getUser();
        $destinataire = array();

        // Si il s'agit de vacances on ajoute le responsable de suivi des vacances
        if ($affectation->getIdentifiantMandat() == 'NSC-Vacances') {
            $system = $em->getRepository('NurunSystemBundle:System')->findAll();
            $destinataire[] = $system[0]->getEmailGestionVacances();
        } else {
            $destinataire = null;
        }
        // $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($affectation->getConseiller());

        // if (!empty($rdp))
        // {
        //     $destinataire[] = $rdp->getRdp()->getCourriel();
        // }

        $alert = $this->renderView('NurunRhBundle:ConseillerMandat:email.txt.twig', array('affectation' => $affectation, 'action' => 'Restauration', 'auteur' => $user->getUserName()));

        // Enfin on appelle le service de courriel pour envoyer la notification
        $notify = $this->get('send.email');
        $notify->notifyVp('Restauration d affectation', $alert, $vp, $destinataire, $user);

        $request->getSession()->getFlashBag()->add('info', "L'affectation a bien été restaurée.");

        return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $affectation->getConseiller()->getId()) ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deletecongeAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $affectation = $em->getRepository('NurunRhBundle:ConseillerMandat')->find($id);

        if (null === $affectation) {
            throw new NotFoundHttpException("L'affectation d'id " . $id . " n'existe pas.");
        }

        if ($affectation->getIdentifiantMandat() != "NSC-Vacances") {
            throw new NotFoundHttpException("Action non autorisée");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {

            $em->remove($affectation);
            $em->flush();

            $user = $this->getUser();
            $body = $this->renderView('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectation, 'Suppression' => 'Ajout', 'user' => $user));
            $notification = $this->get('nurun.notification');
            $notification->notify($request->get('_route'), $body, $affectation);

            // On prépare les arguments nécessaire pour la notification courriel
            // On récupère le secteur du conseiller affecté
            $vp = $affectation->getConseiller()->getvicePresidence()->getAcronyme();
            $user = $this->getUser();
            $destinataire = array();

            // Si il s'agit de vacances on ajoute le responsable de suivi des vacances
            if ($affectation->getIdentifiantMandat() == 'NSC-Vacances') {
                $system = $em->getRepository('NurunSystemBundle:System')->findAll();
                $destinataire[] = $system[0]->getEmailGestionVacances();
            } else {
                $destinataire = null;
            }
            $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($affectation->getConseiller());

            if (!empty($rdp))
            {
                $destinataire[] = $rdp->getRdp()->getCourriel();
            }
            $alert = $this->renderView('NurunRhBundle:ConseillerMandat:email.txt.twig', array('affectation' => $affectation, 'action' => 'Suppression', 'auteur' => $user->getUserName()));

            // Enfin on appelle le service de courriel pour envoyer la notification
            $notify = $this->get('send.email');
            $notify->notifyVp('Suppression d affectation', $alert, $vp, $destinataire, $user);

            $request->getSession()->getFlashBag()->add('info', "L'affectation a bien été supprimée.");

            return $this->redirect($this->generateUrl('nurun_affectation_home'));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('NurunRhBundle:ConseillerMandat:deleteconge.html.twig', array(
            'affectation' => $affectation,
            'form' => $form->createView()
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function rapportVacancesAction($vp, $dateDeb, $dateFin)
    {
        $dateBasse = new \DateTime($dateDeb);
        $dateHaute = new \DateTime($dateFin);

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Rapport des prochaines vacances de nos conseillers")
            ->setSubject("Liste des conseillers partant en congés")
            ->setDescription("Liste des conseillers et de leurs congés")
            ->setKeywords("conseillers kentel congés vacances")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Rapport_Vacances');

        $em = $this->get('doctrine')->getManager();
        // $user = $this->getUser();

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Conseiller')
            ->setCellValue('B' . $numligne, 'Date début')
            ->setCellValue('C' . $numligne, 'Date fin');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('f7f7f7');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne.':C'.$numligne)->getFont()->setBold(true);
        $numligne++;

        // On récupère la liste des conseillers en vacances
        $conseillersVacances = $em->getRepository('NurunRhBundle:ConseillerMandat')->findFuturesVacancesByDates($vp,$dateBasse,$dateHaute);

        foreach($conseillersVacances as $conseillerVacance)
        {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $conseillerVacance[0]->getPrenom() . " " . $conseillerVacance[0]->getNom());
            foreach ($conseillerVacance[1] as $affectationVacance)
            {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B'. $numligne, $affectationVacance->getDateDebut()->format('d-M-Y'));
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C'. $numligne, $affectationVacance->getDateFin()->format('d-M-Y'));
                $numligne++;

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A' . $numligne, $conseillerVacance[0]->getPrenom() . " " . $conseillerVacance[0]->getNom());

            }
        }
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, '');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'rapportvacances_'.$vp.'.xls'
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
    public function rapportAffectsAction(Request $request)
    {

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Rapport des conseillers devenant disponibles")
            ->setSubject("Liste des conseillers finissant leurs affectations")
            ->setDescription("Liste des conseillers triés par la date de fin d'affectation")
            ->setKeywords("conseillers kentel affectations")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Rapport');

        $em = $this->get('doctrine')->getManager();
        $user = $this->getUser();

        // On récupère la liste des conseillers triés par date de libération
        $liste = $em->getRepository('NurunRhBundle:ConseillerMandat')->listByDisponibilite($user->getVp());

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'RGE')
            ->setCellValue('B' . $numligne, 'Conseiller')
            ->setCellValue('C' . $numligne, 'Profil')
            ->setCellValue('D' . $numligne, '%')
            ->setCellValue('E' . $numligne, 'Date début')
            ->setCellValue('F' . $numligne, 'Date fin');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(45);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(5);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne.':F'.$numligne)->getFont()->setBold(true);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne.':F'.$numligne)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);

        $numligne++;
        $numligne++;

        // On récupère la liste des conseillers sur le banc ce mois ci
        $conseillersDispos = $em->getRepository('NurunRhBundle:ConseillerMandat')->findConseillersDisponibles($user->getVp());
        $logger->info('Génération du rapport des affectations avec ' . count($conseillersDispos) . ' conseillers');

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Conseillers actuellement sur le banc');
        $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne.':A'.$numligne)->getFont()->setItalic(true);

        $numligne++;

        foreach ($conseillersDispos as $conseillerDispo) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B' . $numligne, $conseillerDispo->getPrenom() . " " . $conseillerDispo->getNom());
            $actifRdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($conseillerDispo);
            if (!empty($actifRdp))
            {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $actifRdp->getRdp()->getPrenom() . " " . $actifRdp->getRdp()->getNom());
            }
            else
            {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A' . $numligne, "Pas de RGE actif");
            }
            $numligne++;
        }
        $numligne++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Conseillers finissant leurs affectations');
        $phpExcelObject->getActiveSheet()->getStyle('A'.$numligne.':A'.$numligne)->getFont()->setItalic(true);

        $numligne++;

        // create query builder
        foreach ($liste as $item) {
            // On récupère le RDP du conseiller affiché
            $actifRdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($item->getConseiller());

            if (!empty($actifRdp)) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A' . $numligne, $actifRdp->getRdp()->getDisplay());
            }

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B' . $numligne, $item->getConseiller()->getDisplay());

            if (!empty($item->getConseiller()->getPoste())) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C' . $numligne, $item->getConseiller()->getPoste()->getDescription());
            }

            if (!empty($item->getConseiller()->getNbreHeures())) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D' . $numligne, $item->getConseiller()->getNbreHeures() . 'h/s');
            }

            $numligne++;

            // On récupère les affectations du conseiller affiché
            $listeAffectations = $em->getRepository('NurunRhBundle:ConseillerMandat')->findBy(
                array('conseiller' => $item->getConseiller()), array('dateFin' => 'asc')
            );
            if (!empty($listeAffectations)) {
                foreach ($listeAffectations as $affectation) {
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('B' . $numligne, $affectation->getIdentifiantMandat())
                        ->setCellValue('C' . $numligne, $affectation->getStatutAffectation())
                        ->setCellValue('D' . $numligne, $affectation->getPourcentage())
                        ->setCellValue('E' . $numligne, $affectation->getDateDebut()->format('Y m d'));
                    if (!empty($affectation->getDateFin())) {
                        $phpExcelObject->setActiveSheetIndex(0)
                            ->setCellValue('F' . $numligne, $affectation->getDateFin()->format('Y m d'));
                    }
                    $numligne++;
                }
            }
        }


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'rapportconseillers.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
