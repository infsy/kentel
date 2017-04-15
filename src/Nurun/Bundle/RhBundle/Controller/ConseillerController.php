<?php

namespace Nurun\Bundle\RhBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nurun\Bundle\RhBundle\Entity\Conseiller;
use Nurun\Bundle\RhBundle\Entity\ConseillerMandat;
use Nurun\Bundle\RhBundle\Entity\ConseillerFonction;
use Nurun\Bundle\RhBundle\Entity\StatutAffectation;
use Nurun\Bundle\RhBundle\Entity\Document;
use Nurun\Bundle\RhBundle\Entity\Fonction;
use Nurun\Bundle\RhBundle\Entity\Action;
use Nurun\Bundle\RhBundle\Entity\UserNotification;
use Nurun\Bundle\RhBundle\Form\ConseillerType;
use Nurun\Bundle\RhBundle\Form\ConseillerMandatType;
use Nurun\Bundle\RhBundle\Form\ConseillerMandatCongeType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Tests\StringableObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use JpGraph\JpGraph;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;
use Symfony\Component\Validator\Constraints\DateTime;

class ConseillerController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    // Affichage de la liste des conseillers
    public function accueilAction()
    {
        return $this->render(
            '::home.html.twig');
    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    // Affichage de la liste des conseillers
    public function indexAction(Request $request)
    {
        $viewAll = $request->query->get('viewAll');

        $listConseillers = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->findAll();

        // fonction qui compare les valeurs post_name l'une à l'autre

        usort($listConseillers, function ($a, $b) {
            setlocale(LC_COLLATE, 'fr_FR.UTF-8'); // Utilisation du local FR
            return strcoll($a->getPrenom(), $b->getPrenom());
        });


        return $this->render(
            'NurunRhBundle:Conseiller:index.html.twig', array(
            'conseillers' => $listConseillers,
            'mode' => "ALL",
            'viewAll' => $viewAll
        ));
    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    // Affichage de la liste des conseillers et de leurs competences
    public function competencesAction()
    {
        $listConseillers = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->findAllActifs();

        // fonction qui compare les valeurs post_name l'une à l'autre

        usort($listConseillers, function ($a, $b) {
            setlocale(LC_COLLATE, 'fr_FR.UTF-8'); // Utilisation du local FR
            return strcoll($a->getPrenom(), $b->getPrenom());
        });

        $conseillersCompetences = array();

        foreach ($listConseillers as $conseiller) {
            $conseillerCompetence['id'] = $conseiller->getId();
            $conseillerCompetence['nom'] = $conseiller->getPrenom() . ' ' . $conseiller->getNom();
            if (!empty($conseiller->getProfil())) {
                $conseillerCompetence['profil'] = $conseiller->getProfil()->getProfil();
            } else {
                $conseillerCompetence['profil'] = 'Sans profil';
            }
            if (!empty($conseiller->getPoste())) {
                $conseillerCompetence['poste'] = $conseiller->getPoste()->getDescription();
            } else {
                $conseillerCompetence['poste'] = 'Sans poste';
            }
            $competences = '';
            foreach ($conseiller->getCompetences() as $competence) {
                if ($competence->getNiveau()->getForce() == 3) {
                    $competences = $competences . '<B>' . $competence->getCompetence()->getCompetence() . '</B>, ';
                } else {
                    $competences = $competences . $competence->getCompetence()->getCompetence() . ', ';
                }

            }
            $conseillerCompetence['expertises'] = $competences;

            $certifications = '';
            foreach ($conseiller->getCertifications() as $certification) {
                    $certifications = $certifications . $certification->getCertification()->getAcronyme() . ', ';
            }
            $conseillerCompetence['certifications'] = $certifications;

            $languages = '';
            foreach ($conseiller->getLanguages() as $language) {
                if ($language->getNiveau()->getForce() == 3) {
                    $languages = $languages . '<B>' . $language->getLanguage()->getLanguage() . '(' .
                        $language->getNiveau()->getForce() . ')' . '</B>, ';
                } else {
                    $languages = $languages . $language->getLanguage()->getLanguage() . '(' .
                        $language->getNiveau()->getForce() . '), ';
                }
            }
            $conseillerCompetence['languages'] = $languages;

            $conseillersCompetences[] = $conseillerCompetence;
        }

        return $this->render(
            'NurunRhBundle:Conseiller:competences.html.twig', array(
            'conseillers' => $conseillersCompetences));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function localisationAction(Request $request)
    {
        $data = array();

        $adresses = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Adresse')
            ->findAll();

        foreach ($adresses as $adresse) {
            $clients = $this->getDoctrine()
                ->getRepository('NurunRhBundle:Client')
                ->findByAdresse($adresse);

            foreach ($clients as $client) {
                $tab1 = array();
                $tab1['nom'] = $client->getAcronyme();
                $tab1['adresse'] = $adresse->getNumeroAdresse() . ', ' . $adresse->getLigne1Adresse();
                $tab1['ville'] = $adresse->getVille();
                $tab1['code_postal'] = $adresse->getCodePostal();
                $tab1['lat'] = $adresse->getLatitude();
                $tab1['long'] = $adresse->getLongitude();

                $mandats = $this->getDoctrine()
                    ->getRepository('NurunRhBundle:Mandat')
                    ->findByAdresseAndClient($adresse,$client);

                foreach ($mandats as $mandat) {
                    $tab2 = array();
                    $videConseillers = true;
                    $tab2['no'] = $mandat->getIdentifiant();

                    $conseillers = $this->getDoctrine()
                        ->getRepository('NurunRhBundle:Conseiller')
                        ->findByMandatAffecte($mandat->getId());

                    if (!empty($conseillers))
                    {
                        $videConseillers = false;
                    }

                    $tab3 = array();
                    foreach ($conseillers as $conseiller) {
                        $isCoordonnateur = $this->getDoctrine()
                            ->getRepository('NurunRhBundle:Conseiller')
                            ->isCoordoonateurOfMandat($conseiller->getId(),$mandat->getId());
                        if (!is_null($isCoordonnateur))
                        {
                            $tab3[] = '*'.$conseiller->getPrenom() . ' ' . $conseiller->getNom();
                        }
                        else {
                            $tab3[] = $conseiller->getPrenom() . ' ' . $conseiller->getNom();
                        }
                    }
                    $tab2['employes'] = $tab3;
                    if (!$videConseillers) {
                        $tab1['mandats'][] = $tab2;
                    }
                }

                if (array_key_exists('mandats', $tab1)) {
                    $data[] = $tab1;
                }
            }
        }
//      $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
//      $response = new \Symfony\Component\HttpFoundation\Response($jsonData);
//        return $response;
        return $this->render(
            'NurunRhBundle:Conseiller:locations.html.twig', array(
            'locations' => $data
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function exportAction(Request $request)
    {
        // On va travailler avec le $repository des conseillers
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Conseiller');

        // On récupère tous les conseillers
        $conseillers = $repository->findAll();

        // on définit le tableau qui va servir a l'export'
        $conseillersArray = array();


        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($conseillers as $conseiller) {
            if(!($conseiller->isDeleted())){

            $conseillerArray = array();
            $conseillerArray['id'] = $conseiller->getId();
            $conseillerArray['prenom'] = $conseiller->getPrenom();
            $conseillerArray['nom'] = $conseiller->getNom();

            if (empty($conseiller->getVicePresidence())) {
                $conseillerArray['secteur'] = "Inconnu";
            } else {
                $conseillerArray['secteur'] = $conseiller->getVicePresidence()->getAcronyme();
            }

            // comme leur affectations en cours
            $listAffectations = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerMandat')
                ->findAffectationsAffected($conseiller);

            if (empty($listAffectations)) {
                $conseillerArray['affectations'] = "Inconnu";
            } else {
                $conseillerArray['affectations'] = $listAffectations;
            }

            $rdp = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerRdp')
                ->findActifRdp($conseiller);

            if (empty($rdp)) {
                $conseillerArray['rdp'] = "Inconnu";

            } else {
                $conseillerArray['rdp'] = $rdp->getRdp()->getPrenom() . " " . $rdp->getRdp()->getNom();
            }

            if (empty($conseiller->getProfil())) {
                $conseillerArray['profil'] = "Inconnu";
            } else {
                $conseillerArray['profil'] = $conseiller->getProfil()->getProfil();
            }

            if (empty($conseiller->getNumEmploye())) {
                $conseillerArray['numEmploye'] = "Inconnu";
            } else {
                $conseillerArray['numEmploye'] = $conseiller->getNumEmploye();
            }


            $conseillersArray[] = $conseillerArray;
            unset($conseillerArray);
        }
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

        //On trie la liste des conseillers par le prénom
        foreach ($conseillersArray as $key => $row) {
            $prenom[$key] = $row['prenom'];
        }
        //On supprime les accents
        $array_sans_accent = array_map($functionSansAccent, $prenom);
        //On met en minuscule
        $array_lowercase = array_map('strtolower', $array_sans_accent);


        array_multisort($array_lowercase, SORT_ASC, $conseillersArray);

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Liste de nos conseillers")
            ->setSubject("Listing employés")
            ->setDescription("Liste des conseillers")
            ->setKeywords("conseillers kentel listing")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Liste de nos conseillers');

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Conseiller')
            ->setCellValue('B' . $numligne, 'Num Employé')
            ->setCellValue('C' . $numligne, 'Mandats')
            ->setCellValue('D' . $numligne, 'Profil')
            ->setCellValue('E' . $numligne, 'Gestionnaire')
            ->setCellValue('F' . $numligne, 'Secteur');


        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('f7f7f7');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1:B600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1:C600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('E1:E600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('F1:E600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(10);

        $phpExcelObject->getActiveSheet()->getStyle('A' . $numligne . ':F' . $numligne)->getFont()->setBold(true);
        $numligne++;

        foreach ($conseillersArray as $conseiller) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $conseiller['prenom'] . " " . $conseiller['nom']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B' . $numligne, $conseiller['numEmploye']);

            if (($conseiller['affectations'] == "Inconnu")) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C' . $numligne, "Pas de mandat");
            } else {
                $affectationsString = '';
                foreach ($conseiller['affectations'] as $affectation) {
                    $affectationsString .= $affectation->getMandat()->getClient()->getAcronyme() . ' ';
                }
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C' . $numligne, $affectationsString);
            }

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D' . $numligne, $conseiller['profil']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E' . $numligne, $conseiller['rdp']);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F' . $numligne, $conseiller['secteur']);


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
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Employes_Nurun.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }


    /**
     * @Security("has_role('ROLE_USER')")
     */
    // Affichage de l'annuaire
    public function indexAnnuaireAction()
    {
        $conseillers = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->findAll();

        $conseillersArray = array();

        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($conseillers as $conseiller) {
            if($conseiller->isDeleted()){
                continue;
            }
            $conseillerArray = array();
            $conseillerArray['id'] = $conseiller->getId();
            $conseillerArray['prenom'] = $conseiller->getPrenom();
            $conseillerArray['nom'] = $conseiller->getNom();
            $conseillerArray['telephoneNurun'] = $conseiller->getTelephoneNurun();
            $conseillerArray['telephoneMandat'] = $conseiller->getTelephoneMandat();
            $conseillerArray['telephoneAutres'] = $conseiller->getTelephoneAutres();
            $conseillerArray['courriel'] = $conseiller->getCourriel();
            if (empty($conseiller->getVicePresidence())) {
                $conseillerArray['secteur'] = "Inconnu";
            } else {
                $conseillerArray['secteur'] = $conseiller->getVicePresidence()->getAcronyme();
            }

            // comme leur affectations en cours
            $listAffectations = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerMandat')
                ->findAffectationsAffected($conseiller);
            $conseillerArray['affectations'] = $listAffectations;

            // ou leur rge actif
            $rge = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerRdp')
                ->findActifRdp($conseiller);
            $conseillerArray['rge'] = $rge;

            $conseillersArray[] = $conseillerArray;
            unset($conseillerArray);
        }
        return $this->render('NurunRhBundle:Conseiller:indexannuaire.html.twig', array('conseillers' => $conseillersArray));
    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    public function exportAnnuaireAction(Request $request)
    {
        // On va travailler avec le $repository des conseillers
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Conseiller');

        // On récupère les conseillers ayant leur fête dans l'intervalle de date'
        $conseillers = $repository->findAll();

        $conseillersArray = array();


        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($conseillers as $conseiller) {
            if($conseiller->isDeleted()){
                continue;
            }
            $conseillerArray = array();
            $conseillerArray['id'] = $conseiller->getId();
            $conseillerArray['prenom'] = $conseiller->getPrenom();
            $conseillerArray['nom'] = $conseiller->getNom();
            $conseillerArray['telephoneNurun'] = $conseiller->getTelephoneNurun();
            $conseillerArray['telephoneMandat'] = $conseiller->getTelephoneMandat();
            $conseillerArray['telephoneAutres'] = $conseiller->getTelephoneAutres();
            if (empty($conseiller->getVicePresidence())) {
                $conseillerArray['secteur'] = "Inconnu";
            } else {
                $conseillerArray['secteur'] = $conseiller->getVicePresidence()->getAcronyme();
            }

            // comme leur affectations en cours
            $listAffectations = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerMandat')
                ->findAffectationsAffected($conseiller);
            $conseillerArray['affectations'] = $listAffectations;

            $conseillersArray[] = $conseillerArray;
            unset($conseillerArray);
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

        //On trie la liste des conseillers par le prénom
        foreach ($conseillersArray as $key => $row) {
            $prenom[$key] = $row['prenom'];
        }
        //On supprime les accents
        $array_sans_accent = array_map($functionSansAccent, $prenom);
        //On met en minuscule
        $array_lowercase = array_map('strtolower', $array_sans_accent);


        array_multisort($array_lowercase, SORT_ASC, $conseillersArray);

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Annuaire de nos conseillers")
            ->setSubject("Annuaire Téléphonique")
            ->setDescription("Liste des conseillers avec leurs coordonnées téléphoniques")
            ->setKeywords("conseillers kentel téléphone annuaire")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Annuaire téléphonique');

        // $em = $this->get('doctrine')->getManager();
        // $user = $this->getUser();

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Conseiller')
            ->setCellValue('B' . $numligne, 'Nurun')
            ->setCellValue('C' . $numligne, 'Mandats')
            ->setCellValue('D' . $numligne, 'Téléphone Mandat')
            ->setCellValue('E' . $numligne, 'Téléphones autres');

        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('f7f7f7');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1:B600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1:C600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1:D600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('E1:E600')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(25);

        $phpExcelObject->getActiveSheet()->getStyle('A' . $numligne . ':E' . $numligne)->getFont()->setBold(true);
        $numligne++;

        foreach ($conseillersArray as $conseiller) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $conseiller['prenom'] . " " . $conseiller['nom']);
            if (!empty($conseiller['telephoneNurun'])) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B' . $numligne, $conseiller['telephoneNurun']);
            } else {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B' . $numligne, '-');
            }
            if (empty($conseiller['affectations'])) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C' . $numligne, "Pas de mandat");
            } else {
                $affectationsString = '';
                foreach ($conseiller['affectations'] as $affectation) {
                    $affectationsString .= $affectation->getMandat()->getClient()->getAcronyme() . ' ';
                }
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C' . $numligne, $affectationsString);
            }
            if (!empty($conseiller['telephoneMandat'])) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D' . $numligne, $conseiller['telephoneMandat']);
            } else {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D' . $numligne, '-');
            }
            if (!empty($conseiller['telephoneAutres'])) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E' . $numligne, $conseiller['telephoneAutres']);
            } else {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E' . $numligne, '-');
            }

            $numligne++;

        }
        // $phpExcelObject->setActiveSheetIndex(0)
            // ->setCellValue('A' . $numligne, '');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Annuaire_Nurun.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Security("has_role('ROLE_RDP')")
     */
    // Affichage de la liste de mes conseillers en tant que RGE
    public function listMesRessourcesAction(Request $request)
    {
        $user = $this->getUser();
        $viewAll = $request->query->get('viewAll');

        $conseillersAffichables = array();

        // On récupère sous forme d'entité l'utilisateur connecté
        $conseillerConnected = $this->getDoctrine()
            ->getRepository('NurunRhBundle:Conseiller')
            ->findOneByCourriel($user->getEmail());

        if (empty($conseillerConnected)) {
            $logger = $this->get('logger');
            $logger->error('Un profil n a pas été reconnu dans la base : ' . $user->getEmail());
            $notify = $this->get('send.email');
            $notify->notifyAdmin("Profil " . $user->getEmail() . " incorrectement renseigné dans Kentel");
            throw new NotFoundHttpException('Votre profil n est pas reconnu dans les employés !');
            exit;
        } else {
            $listConseillers = $this->getDoctrine()
                ->getRepository('NurunRhBundle:Conseiller')
                ->findAll();

            foreach ($listConseillers as $conseiller) {
                if($conseiller->isDeleted()){
                    continue;
                }
                // Recherche le RDP du conseiller testé
                $currentRDP = $this->getDoctrine()
                    ->getRepository('NurunRhBundle:ConseillerRdp')
                    ->findActifRdp($conseiller);

                if (!empty($currentRDP)) {

                    // Si le RDP de ce conseiller est l'utilisateur connecté alors on va garder ce conseiller
                    if ($currentRDP->getRdp() == $conseillerConnected) {

                        $conseillerArray = array();
                        $conseillerArray['id'] = $conseiller->getId();
                        $conseillerArray['prenom'] = $conseiller->getPrenom();
                        $conseillerArray['nom'] = $conseiller->getNom();
                        $conseillerArray['poste'] = $conseiller->getPoste();
                        $conseillerArray['profil'] = $conseiller->getProfil();
                        $conseillerArray['vicePresidence'] = $conseiller->getVicePresidence();
                        $conseillerArray['isDeleted'] = $conseiller->isDeleted();

                        $conseillersAffichables[] = $conseillerArray;
                        unset($conseillerArray);
                    }
                }
            }

            return $this->render(
                'NurunRhBundle:Conseiller:index.html.twig', array(
                'conseillers' => $conseillersAffichables,
                'mode' => "RGE",
                'viewAll' => $viewAll));
        }
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    // Chargement d'un fichier de conseillers
    public function uploadAction()
    {
        // On crée un objet Mandat
        $document = new Document();

        $form = $this->createFormBuilder($document)
            ->add('name')
            ->add('file')
            ->getForm();

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($document);
                $em->flush();
                $logger = $this->get('logger');

                // Create and configure the reader
                $logger->info('chargement de ' . $document->getAbsolutePath());

                $file = new \SplFileObject($document->getAbsolutePath());
                $csvReader = new CsvReader($file);
                $logger->info('comprend x lignes ' . $csvReader->count());

                // Tell the reader that the first row in the CSV file contains column headers
                $csvReader->setHeaderRowNumber(0);

                foreach ($csvReader as $row) {
                    $conseiller = $em->getRepository('NurunRhBundle:Conseiller')
                        ->findOneByName($row['nom'], $row['prenom']);
                    if (empty($conseiller)) {
                        $conseiller = new Conseiller();
                        $conseiller->setNom($row['nom']);
                        $conseiller->setPrenom($row['prenom']);
                        $conseiller->setActif(TRUE);
                        $conseiller->setDateArrivee(new \DateTime($row['dateEntree']));
                        $conseiller->setPoste($row['poste']);
                        $conseiller->setRole($row['role']);
                        $conseiller->setNbreHeures($row['nbeheure']);
                        $conseiller->setStatut($row['statut']);
                    } else {
                        $conseiller->setDateArrivee(new \DateTime($row['dateEntree']));
                        $conseiller->setPoste($row['poste']);
                        $conseiller->setRole($row['role']);
                        $conseiller->setNbreHeures($row['nbeheure']);
                        $conseiller->setStatut($row['statut']);
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($conseiller);
                    $em->flush();
                }
                $this->redirect($this->generateUrl('nurun_conseiller_home'));
            }
        }
        return $this->render(
            'NurunRhBundle:Conseiller:upload.html.twig', array(
            'form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_ROOT')")
     */
    // Chargement d'un fichier de conseillers
    public function uploadRdpAction()
    {
        // On crée un objet Mandat
        $document = new Document();

        $form = $this->createFormBuilder($document)
            ->add('name')
            ->add('file')
            ->getForm();

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($document);
                $em->flush();
                $logger = $this->get('logger');

                // Create and configure the reader
                $logger->info('chargement de ' . $document->getAbsolutePath());

                $file = new \SplFileObject($document->getAbsolutePath());
                $csvReader = new CsvReader($file);
                $logger->info('comprend x lignes ' . $csvReader->count());

                // Tell the reader that the first row in the CSV file contains column headers
                $csvReader->setHeaderRowNumber(0);
                $em = $this->getDoctrine()->getManager();

                foreach ($csvReader as $row) {
                    $conseiller = $em->getRepository('NurunRhBundle:Conseiller')
                        ->findOneBy(
                            array('nom' => $row['nom'], 'prenom' => $row['prenom'])
                        );
                    if (!empty($conseiller)) {
                        $rdp = $em->getRepository('NurunRhBundle:Conseiller')
                            ->findOneBy(
                                array('nom' => $row['nomrdp'], 'prenom' => $row['prenomrdp'])
                            );
                        if (!empty($rdp)) {
                            $conseillerrdp = new \Nurun\Bundle\RhBundle\Entity\ConseillerRdp;
                            $conseillerrdp->setConseiller($conseiller);
                            $conseillerrdp->setRdp($rdp);
                            $em->persist($conseillerrdp);
                            // $conseiller->addRdp($rdp);
                            $em->flush();
                        }
                    }
                }
                $this->redirect($this->generateUrl('nurun_rh_home'));
            }
        }
        return $this->render(
            'NurunRhBundle:Conseiller:upload.html.twig', array(
            'form' => $form->createView()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    // Affichage du détail d'un conseiller
    public function viewAction($id, Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $origine = $request->getBaseUrl();
        $em = $this->getDoctrine()->getManager();

        // On récupère le repository
        $repository = $em->getRepository('NurunRhBundle:Conseiller');

        // On récupère l'entité correspondante à l'id $id
        $conseiller = $repository->find($id);

        // $conseiller est donc une instance de Nurun\RhBundle\Entity\Conseiller
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $conseiller) {
            throw new NotFoundHttpException("Le conseiller d'id " . $id . " n'existe pas.");
        }

        if($conseiller->isDeleted()){
            if (!$this->get('security.context')->isGranted('ROLE_GESTION')) {
                throw new AccessDeniedException('Impossible de voir un conseiller archivé.');
            }
        }

        if ((!$this->get('security.authorization_checker')->isGranted('ROLE_RDP'))
            and ($user->getEmail() != $conseiller->getCourriel()))
        {
            throw $this->createAccessDeniedException();
        }

        $repository = $em->getRepository('NurunRhBundle:ConseillerRdp');

        $conseillerRdp = $repository->findActifRdp($conseiller);

        $dateTimeStart = (new \Datetime())->setTimestamp(strtotime('now'));
        $dateStart = $dateTimeStart->format('Y-m-d');
        $dateTimeEnd = (new \Datetime())->setTimestamp(strtotime('today +12 weeks'));
        $dateEnd = $dateTimeEnd->format('Y-m-d');

        $affectations = $em->getRepository('NurunRhBundle:ConseillerMandat')->findAffectationsAffected($conseiller);
        $affectationsPossibles = array();
        foreach ($affectations as $affectation) {
            $dateDebutAffectation = $affectation->getDateDebut()->format('Y-m-d');
            $dateFinAffectation = $affectation->getDateFin()->format('Y-m-d');
            $idAffectation = $affectation->getId();

            if((strcmp($dateDebutAffectation, $dateStart) > 0 && strcmp($dateDebutAffectation, $dateEnd) < 0) || (strcmp($dateFinAffectation, $dateStart) > 0 && strcmp($dateFinAffectation, $dateEnd < 0))) {
                $affectationsPossibles[$idAffectation]['dateDebut'] = $dateDebutAffectation;
                $affectationsPossibles[$idAffectation]['dateFin'] = $dateFinAffectation;
                $affectationsPossibles[$idAffectation]['pourcentage'] = $affectation->getPourcentage();
            }
        }

        $propositions = $em->getRepository('NurunRhBundle:ConseillerMandat')->findPropositions($conseiller);
        $propositionsPossibles = array();
        foreach ($propositions as $proposition) {
            $dateDebutProposition = $proposition->getDateDebut()->format('Y-m-d');
            $dateFinProposition = $proposition->getDateFin()->format('Y-m-d');
            $idProposition = $proposition->getId();

            if((strcmp($dateDebutProposition, $dateStart) > 0 && strcmp($dateDebutProposition, $dateEnd) < 0) || (strcmp($dateFinProposition, $dateStart) > 0 && strcmp($dateFinProposition, $dateEnd < 0))) {
                $propositionsPossibles[$idProposition]['dateDebut'] = $dateDebutProposition;
                $propositionsPossibles[$idProposition]['dateFin'] = $dateFinProposition;
                $propositionsPossibles[$idProposition]['pourcentage'] = $proposition->getPourcentage();
            }
        }

        $stats = array();
        $date = $dateTimeStart;
        $dateString = $dateStart;

        //trouver la date du lundi de cette semaine
        $dayOfWeek = $date->format('w');
        if($dayOfWeek == 0) {
            $dateString = $date->add(date_interval_create_from_date_string("1 days"))->format('Y-m-d');
            $weekString = "semaine du ".$date->format('d/m');
        }
        else if($dayOfWeek == 1) {
            $weekString = "semaine du ".$date->format('d/m');
        }
        else if($dayOfWeek > 1) {
            while($dayOfWeek != 1) {
                $dateString = $date->sub(date_interval_create_from_date_string("1 days"))->format('Y-m-d');
                $dayOfWeek = $date->format('w');
            }
            $weekString = "semaine du ".$date->format('d/m');
        }        

        while(strcmp($dateString, $dateEnd) < 1) {
            $dayOfWeek = $date->format('w');
            if($dayOfWeek == 0 || $dayOfWeek == 6){
                $dateString = $date->add(date_interval_create_from_date_string("1 days"))->format('Y-m-d');
                continue;
            }
            else if($dayOfWeek == 1) {
                $weekString = "semaine du ".$date->format('d/m');
            }

            //Pour les affectations
            $datePourcentage = 0;
            foreach ($affectationsPossibles as $affectation) {
                $dateDebutAffectation = $affectation['dateDebut'];
                $dateFinAffectation = $affectation['dateFin'];

                if(strcmp($dateDebutAffectation, $dateString) <= 0 && strcmp($dateFinAffectation, $dateString) >= 0){
                    $datePourcentage += $affectation['pourcentage'];
                }
            }
            if(isset($stats[$weekString]['affectation'])) {
                if($stats[$weekString]['affectation'] < $datePourcentage) {
                    $stats[$weekString]['affectation'] = $datePourcentage;
                }   
            }
            else {
                $stats[$weekString]['affectation'] = $datePourcentage;
            }

            //Pour les propositions
            $datePourcentageAvecProposition = $datePourcentage;
            foreach ($propositionsPossibles as $proposition) {
                $dateDebutProposition = $proposition['dateDebut'];
                $dateFinProposition = $proposition['dateFin'];

                if(strcmp($dateDebutProposition, $dateString) <= 0 && strcmp($dateFinProposition, $dateString) >= 0){
                    $datePourcentageAvecProposition += $proposition['pourcentage'];
                }
            }
            if(isset($stats[$weekString]['proposition'])) {
                if($stats[$weekString]['proposition'] < $datePourcentageAvecProposition) {
                    $stats[$weekString]['proposition'] = $datePourcentageAvecProposition;
                }   
            }
            else {
                $stats[$weekString]['proposition'] = $datePourcentageAvecProposition;
            }
            
            $dateString = $date->add(date_interval_create_from_date_string("1 days"))->format('Y-m-d');
        }

        // On prépare la liste des mois en français afin de convertir le retour de la commande format
        $moisfr = array('January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars', 'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
            'July' => 'Juillet', 'August' => 'Aout', 'September' => 'Septembre', 'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre');

        if (!empty($conseiller->getDateFete())) {
            $dateFete = $conseiller->getDateFete()->format('d') . " " . $moisfr[$conseiller->getDateFete()->format('F')];
        } else {
            $dateFete = "non définie";
        }

        $ressourcesAffectees = $em->getRepository('NurunRhBundle:ConseillerRdp')->findRessourcesByRdp($conseiller);

        $form = $this->createFormBuilder()
            ->add('photo', FileType::class)
            ->add('save', 'submit', array('label' => 'Enregistrer'))
            ->getForm();

        if ($form->handleRequest($request)->isValid()) {
            $file = $form->get('photo');

            $strm = fopen($file->getData()->getRealPath(), 'rb');
            $conseiller->setPhoto(stream_get_contents($strm));

            $em->persist($conseiller);
            $em->flush();
            $em->clear();
            return $this->redirectToRoute('nurun_conseiller_view', array('id' => $id));
        }

        $photo = $conseiller->getPhoto();
        if (!is_null($photo)) {
            $photo = base64_encode($photo);
        }

        $mandataireOuChargeProjet = $em->getRepository('NurunRhBundle:Mandat')->findByMandataireOrChargeProjet($conseiller);

        return $this->render('NurunRhBundle:Conseiller:view.html.twig', array(
            'conseiller'                => $conseiller,
            'dateFete'                  => $dateFete,
            'conseillerRdp'             => $conseillerRdp,
            'affectations'              => $affectations,
            'propositions'              => $propositions,
            'photo'                     => $photo,
            'form'                      => $form->createView(),
            'ressources'                => $ressourcesAffectees,
            'origine'                   => $origine,
            'stats'                     => $stats,
            'mandataireOuChargeProjet'  => $mandataireOuChargeProjet
            )
        );
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    // Création d'un conseiller
    public function addAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_GESTION')) {

            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux gestionnaires.');
        }
        
        // $request->request->add(array('origine' => 'conseillerMandat'));

        $conseiller = new Conseiller();

        $form = $this->get('form.factory')->create(new ConseillerType(), $conseiller);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();

            $session = $this->getRequest()->getSession();
            $origine = $request->request->get("origine");
            $session->set('origine', $origine);
            $session->getFlashBag()->add('notice', 'Conseiller bien enregistré.');

            return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $conseiller->getId())));
        }

        return $this->render('NurunRhBundle:Conseiller:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    // Création d'un conseiller
    public function addCongeAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        // On récupère le repository
        $repository = $em->getRepository('NurunRhBundle:Conseiller');

        // On récupère l'entité correspondante à l'id $id
        $conseiller = $repository->find($id);

        // $conseiller est donc une instance de Nurun\RhBundle\Entity\Conseiller
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $conseiller) {
            throw new NotFoundHttpException("Le conseiller d'id " . $id . " n'existe pas.");
        }

        //On récupère le mandat correspondant aux congés
        $mandat = $em->getRepository('NurunRhBundle:Mandat')
            ->findOneByIdentifiant('Vacances');

        if (null === $mandat) {
            throw new NotFoundHttpException("Le mandat Vacances n'existe pas.");
        }

        //On récupère le statut affectation correspondant a "A"
        $statut = $em->getRepository('NurunRhBundle:StatutAffectation')
            ->findOneByAcronyme('A');

        if (null === $mandat) {
            throw new NotFoundHttpException("Le mandat Vacances n'existe pas.");
        }
        $conseillermandat = new ConseillerMandat();
        $conseillermandat->setConseiller($conseiller);
        $conseillermandat->setStatutAffectation($statut);
        $conseillermandat->setMandat($mandat);
        $conseillermandat->setPourcentage('100');
        $form = $this->get('form.factory')->create(new ConseillerMandatCongeType(), $conseillermandat);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseillermandat);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $conseiller->getId())));
        }

        return $this->render('NurunRhBundle:Conseiller:editmandatconge.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    // Création d'un conseiller
    public function rapportExecutifProfilAction($profilId)
    {
        $profil = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:ProfilConseiller')
            ->find($profilId);

        // On va travailler avec le $repository des conseillers
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Conseiller');

        // On récupère les conseillers permanents et contractuels relatifs à cette VP'
        $conseillers = $repository->findByProfilExceptPigistes($profilId);
        $nbConseillers = count($conseillers);

        JpGraph::load();
        JpGraph::module('bar');

        $i = 0;
        $expertisesArray = array();
        $tableauConseillers = array();
        $englishLanguagesArray = array();
        $experienceConseillers = array(0, 0, 0, 0);

        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($conseillers as $conseiller) {


            if (!empty($conseiller->getCompetences())) {
                foreach ($conseiller->getCompetences() as $conseillerCompetence) {
                    if (!(array_key_exists($conseillerCompetence->getCompetence()->getCompetence(), $expertisesArray))) {
                        $expertisesArray[$conseillerCompetence->getCompetence()->getCompetence()] = 0;
                    }
                    $expertisesArray[$conseillerCompetence->getCompetence()->getCompetence()]++;
                }
            }
            if (empty($conseiller->getLanguages())) {
                if (!array_key_exists('inconnu', $englishLanguagesArray)) {
                    $englishLanguagesArray['inconnu'] = 0;
                }
                $englishLanguagesArray['inconnu']++;
            } else {
                if ($repository->isEnglishSpoken($conseiller->getId())) {
                    // On charge le niveau d'anglais du conseiller une fois pour toute
                    $englishLevel = strval($repository->englishLevelSpoken($conseiller->getId()));
                    if (!(array_key_exists($englishLevel, $englishLanguagesArray))) {
                        $englishLanguagesArray[$englishLevel] = 0;
                    }
                    $englishLanguagesArray[$englishLevel]++;
                }
            }

            $an1 = (new \DateTime("now"))->format('Y');
            $an2 = $conseiller->getDateArrivee()->format('Y');

            $interval = $an1 - $an2;
            $experience = $interval + $conseiller->getExperienceAnnees();

            $tableauConseillers[$i]['nom'] = $conseiller->getNom();
            $tableauConseillers[$i]['prenom'] = $conseiller->getPrenom();

            $tableauConseillers[$i]['dateArrivee'] = $conseiller->getDateArrivee();
            $tableauConseillers[$i]['experienceAnnees'] = $conseiller->getExperienceAnnees();
            $tableauConseillers[$i]['experienceMois'] = $conseiller->getExperienceMois();
            $tableauConseillers[$i]['interval'] = $interval;
            $tableauConseillers[$i]['experience'] = $experience;

            $i++;

            if ($experience < 6) {
                $experienceConseillers[0]++;

            } elseif ($experience < 11) {
                $experienceConseillers[1]++;

            } elseif ($experience < 16) {
                $experienceConseillers[2]++;
            } else {
                $experienceConseillers[3]++;
            }
        }

        $graphProfil1 = new \Graph(400, 400);
        $graphProfil1->SetShadow();
        $graphProfil1->SetScale('textlin');
        $graphProfil1->title->Set('Expérience  ' . $profil->getProfil());


        $bar1 = new \BarPlot(array_values($experienceConseillers));
        $graphProfil1->xaxis->SetTickLabels(array('0-5', '6-10', '11-15', '16+'));
        $graphProfil1->xaxis->SetLabelMargin(5);
        $graphProfil1->Add($bar1);
        $bar1->SetColor('black');
        $graphProfil1->Stroke(_IMG_HANDLER);
        ob_start();
        $graphProfil1->img->Stream();
        $image_data = ob_get_contents();
        ob_end_clean();
        $bar1 = base64_encode($image_data);


        arsort($expertisesArray);

        $biggestsExpertises = array_slice($expertisesArray, 0, 10);
        $listeExpertises = array_keys($biggestsExpertises);
        for ($i = 0; $i < count($listeExpertises); $i++) {
            $listeExpertises[$i] = $listeExpertises[$i] . '(%d)';
        }

        JpGraph::module('pie');
        $graph5 = new \PieGraph(900, 600);
        $graph5->SetShadow();
        $graph5->title->Set('Répartition des 10 expertises les plus répandues');
        $p5 = new \PiePlot(array_values($biggestsExpertises));
        $p5->value->SetColor('black');
        // $p5->SetLabelType(PIE_VALUE_PER);
        $p5->SetLabelPos(0.75);
        $p5->SetLabels($listeExpertises);

        // $p5->SetLabelPos(1);
        $graph5->Add($p5);
        if (!empty($biggestsExpertises)) {
            $graph5->Stroke(_IMG_HANDLER);
            ob_start();
            $graph5->img->Stream();
            $image_data = ob_get_contents();
            ob_end_clean();
            $pie5 = base64_encode($image_data);
        }
        else {
            $pie5 = null;
        }


        // Graphique de répartition des niveaux en Anglais
        $listeNiveaux = array_keys($englishLanguagesArray);
        for ($i = 0; $i < count($listeNiveaux); $i++) {
            if ($listeNiveaux[$i] == '1')
                $listeNiveaux[$i] = 'Faible' . '(%d%%)';
            elseif ($listeNiveaux[$i] == '2')
                $listeNiveaux[$i] = 'Moyen' . '(%d%%)';
            elseif ($listeNiveaux[$i] == '3')
                $listeNiveaux[$i] = 'Bon' . '(%d%%)';
            else
                $listeNiveaux[$i] = $listeNiveaux[$i] . '(%d)';
        }
        $graph6 = new \PieGraph(900, 600);
        $graph6->SetShadow();
        $graph6->title->Set('Répartition par niveau d"anglais');
        $p6 = new \PiePlot(array_values($englishLanguagesArray));
        $p6->value->SetColor('black');
        $p6->SetLabelPos(0.75);
        $p6->SetLabels($listeNiveaux);
        $graph6->Add($p6);
        if (!empty($englishLanguagesArray)) {
            $graph6->Stroke(_IMG_HANDLER);
            ob_start();
            $graph6->img->Stream();
            $image_data = ob_get_contents();
            ob_end_clean();
            $pie6 = base64_encode($image_data);
        }
        else {
            $pie6 = null;
        }

        return $this->render('NurunRhBundle:Conseiller:rapportExecutifProfil.html.twig', array(
            'graph1' => $bar1,
            'graph2' => $pie5,
            'graph3' => $pie6,
            'nbConseillers' => $nbConseillers,
            'conseillers' => $conseillers,
            'tableau' => $tableauConseillers
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    // Création d'un conseiller
    public function rapportExecutifAction($vp)
    {

        // On va travailler avec le $repository des conseillers
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Conseiller');

        if ($vp == 'TOUT') {
            $vp = 'VPTS';
        }
        // On récupère les conseillers permanents et contractuels relatifs à cette VP'
        $conseillers = $repository->findByVpExceptPigistes($vp);


        JpGraph::load();
        JpGraph::module('bar');


        $profilsArray = array();
        $expertisesArray = array();
        $englishLanguagesArray = array();

        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($conseillers as $conseiller) {

            if (empty($conseiller->getProfil())) {
                if (!array_key_exists('unknown', $profilsArray)) {
                    $profilsArray['unknown'] = 0;
                }
                $profilsArray['unknown']++;
            } else {
                if (!(array_key_exists($conseiller->getProfil()->getProfil(), $profilsArray))) {
                    $profilsArray[$conseiller->getProfil()->getProfil()] = 0;
                }
                $profilsArray[$conseiller->getProfil()->getProfil()]++;
            }
            if (!empty($conseiller->getCompetences())) {
                foreach ($conseiller->getCompetences() as $conseillerCompetence) {
                    if (!(array_key_exists($conseillerCompetence->getCompetence()->getCompetence(), $expertisesArray))) {
                        $expertisesArray[$conseillerCompetence->getCompetence()->getCompetence()] = 0;
                    }
                    $expertisesArray[$conseillerCompetence->getCompetence()->getCompetence()]++;
                }
            }
            if (empty($conseiller->getLanguages())) {
                if (!array_key_exists('inconnu', $englishLanguagesArray)) {
                    $englishLanguagesArray['inconnu'] = 0;
                }
                $englishLanguagesArray['inconnu']++;
            } else {
                if ($repository->isEnglishSpoken($conseiller->getId())) {
                    // On charge le niveau d'anglais du conseiller une fois pour toute
                    $englishLevel = strval($repository->englishLevelSpoken($conseiller->getId()));
                    if (!(array_key_exists($englishLevel, $englishLanguagesArray))) {
                        $englishLanguagesArray[$englishLevel] = 0;
                    }
                    $englishLanguagesArray[$englishLevel]++;
                }
            }
        }

        asort($profilsArray);

        $graph = new \Graph(1000, 600);
        $graph->SetShadow();
        $graph->title->Set('Répartition par profil');
        // $graph->title->SetFont(FF_ARIAL, FS_BOLD, 11);
        $graph->SetScale('textlin');

        $b1 = new \BarPlot(array_values($profilsArray));
        $graph->xaxis->SetTickLabels(array_keys($profilsArray));
        $graph->xaxis->SetLabelAngle(45);
        $graph->xaxis->SetLabelMargin(5);
        $graph->SetMargin(240, 2, 2, 240);
        // $graph->xaxis->title->SetFont(FF_ARIAL, FS_BOLD, 10);
        $graph->Add($b1);
        // $b1->ShowBorder();
        $b1->SetColor('black');

        $graph->Stroke(_IMG_HANDLER);
        ob_start();
        $graph->img->Stream();
        $image_data = ob_get_contents();
        ob_end_clean();
        $bar1 = base64_encode($image_data);

        // $nbrProfils = count($conseillersArray);
        // $nbrProfils--;

        arsort($profilsArray);

        $biggestsProfils = array_slice($profilsArray, 0, 4);
        $listeProfils = array_keys($biggestsProfils);
        $i = 1;

        foreach ($listeProfils as $profil) {
            ${'graphProfil' . $i} = new \Graph(400, 400);
            ${'graphProfil' . $i}->SetShadow();
            ${'graphProfil' . $i}->SetScale('textlin');
            ${'graphProfil' . $i}->title->Set('Expérience des ' . $profil);
            $experienceConseillers = array(0, 0, 0, 0);
            $conseillersByProfil = $repository->findByProfil($profil);
            if (!empty($conseillersByProfil)) {
                foreach ($conseillersByProfil as $conseiller) {

                    $an1 = (new \DateTime("now"))->format('Y');
                    $an2 = $conseiller->getDateArrivee()->format('Y');

                    $interval = $an1 - $an2;
                    $experience = $interval + $conseiller->getExperienceAnnees();

                    if ($experience < 6) {
                        $experienceConseillers[0]++;

                    } elseif ($experience < 11) {
                        $experienceConseillers[1]++;

                    } elseif ($experience < 16) {
                        $experienceConseillers[2]++;
                    } else {
                        $experienceConseillers[3]++;
                    }
                }
            }
            ${'bProfil' . $i} = new \BarPlot(array_values($experienceConseillers));
            ${'graphProfil' . $i}->SetScale('textint');
            ${'graphProfil' . $i}->xaxis->SetTickLabels(array('0-5', '6-10', '11-15', '16+'));
            ${'graphProfil' . $i}->xaxis->SetLabelMargin(5);
            ${'graphProfil' . $i}->Add(${'bProfil' . $i});
            ${'bProfil' . $i}->SetColor('black');
            ${'graphProfil' . $i}->Stroke(_IMG_HANDLER);
            ob_start();
            ${'graphProfil' . $i}->img->Stream();
            $image_data = ob_get_contents();
            ob_end_clean();
            ${'barProfil' . $i} = base64_encode($image_data);
            $i++;
        }

        arsort($expertisesArray);

        $biggestsExpertises = array_slice($expertisesArray, 0, 10);
        $listeExpertises = array_keys($biggestsExpertises);
        for ($i = 0; $i < count($listeExpertises); $i++) {
            $listeExpertises[$i] = $listeExpertises[$i] . '(%d)';
        }

        JpGraph::module('pie');
        $graph5 = new \PieGraph(900, 600);
        $graph5->SetShadow();
        $graph5->title->Set('Répartition des 10 expertises les plus répandues');
        $p5 = new \PiePlot(array_values($biggestsExpertises));
        $p5->value->SetColor('black');
        // $p5->SetLabelType(PIE_VALUE_PER);
        $p5->SetLabelPos(0.75);
        $p5->SetLabels($listeExpertises);

        // $p5->SetLabelPos(1);
        $graph5->Add($p5);
        $graph5->Stroke(_IMG_HANDLER);
        ob_start();
        $graph5->img->Stream();
        $image_data = ob_get_contents();
        ob_end_clean();
        $pie5 = base64_encode($image_data);

        // Graphique de répartition des niveaux en Anglais
        $listeNiveaux = array_keys($englishLanguagesArray);
        for ($i = 0; $i < count($listeNiveaux); $i++) {
            if ($listeNiveaux[$i] == '1')
                $listeNiveaux[$i] = 'Faible' . '(%d%%)';
            elseif ($listeNiveaux[$i] == '2')
                $listeNiveaux[$i] = 'Moyen' . '(%d%%)';
            elseif ($listeNiveaux[$i] == '3')
                $listeNiveaux[$i] = 'Bon' . '(%d%%)';
            else
                $listeNiveaux[$i] = $listeNiveaux[$i] . '(%d)';
        }
        $graph6 = new \PieGraph(900, 600);
        $graph6->SetShadow();
        $graph6->title->Set('Répartition par niveau d"anglais');
        $p6 = new \PiePlot(array_values($englishLanguagesArray));
        $p6->value->SetColor('black');
        $p6->SetLabelPos(0.75);
        $p6->SetLabels($listeNiveaux);
        $graph6->Add($p6);
        $graph6->Stroke(_IMG_HANDLER);
        ob_start();
        $graph6->img->Stream();
        $image_data = ob_get_contents();
        ob_end_clean();
        $pie6 = base64_encode($image_data);

        return $this->render('NurunRhBundle:Conseiller:rapportExecutif.html.twig', array(
            'graph1' => $bar1,
            'graph2' => $barProfil1,
            'graph3' => $barProfil2,
            'graph4' => $barProfil3,
            'graph5' => $barProfil4,
            'graph6' => $pie5,
            'graph7' => $pie6,
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    // Création d'un conseiller
    public function rapportfetesAction($vp, $dateDeb, $dateFin)
    {

        $dateBasse = new \DateTime($dateDeb);
        $dateHaute = new \DateTime($dateFin);

        // On va travailler avec le $repository des conseillers
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Conseiller');

        // On récupère les conseillers ayant leur fête dans l'intervalle de date'
        $conseillers = $repository->findFetesAvenir($vp, $dateBasse, $dateHaute);

        $conseillersArray = array();

        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($conseillers as $conseiller) {
            $conseillerArray = array();
            $conseillerArray['prenom'] = $conseiller->getPrenom();
            $conseillerArray['nom'] = $conseiller->getNom();
            $conseillerArray['dateFete'] = $conseiller->getDateFete();

            // comme leur affectations en cours
            $listAffectations = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerMandat')
                ->findAffectationsAffected($conseiller);
            $conseillerArray['affectations'] = $listAffectations;

            // ou leur rge actif
            $rge = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerRdp')
                ->findActifRdp($conseiller);
            $conseillerArray['rge'] = $rge;

            $conseillersArray[] = $conseillerArray;
            unset($conseillerArray);
        }


        return $this->render('NurunRhBundle:Conseiller:gridfetes.html.twig', array('conseillers' => $conseillersArray, 'vp' => $vp,
            'dateDeb' => $dateDeb, 'dateFin' => $dateFin));
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function saveFetesAction($vp, $dateDeb, $dateFin, Request $request)
    {

        $dateBasse = new \DateTime($dateDeb);
        $dateHaute = new \DateTime($dateFin);

        // On va travailler avec le $repository des conseillers
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Conseiller');

        // On récupère les conseillers ayant leur fête dans l'intervalle de date'
        $conseillers = $repository->findFetesAvenir($vp, $dateBasse, $dateHaute);

        $conseillersArray = array();

        // On enrichit cette liste dans un tableau avec des indications supplémentaires
        foreach ($conseillers as $conseiller) {
            $conseillerArray = array();
            $conseillerArray['prenom'] = $conseiller->getPrenom();
            $conseillerArray['nom'] = $conseiller->getNom();
            $conseillerArray['dateFete'] = $conseiller->getDateFete();

            // comme leur affectations en cours
            $listAffectations = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerMandat')
                ->findAffectationsAffected($conseiller);
            $conseillerArray['affectations'] = $listAffectations;

            // ou leur rge actif
            $rge = $this->getDoctrine()
                ->getRepository('NurunRhBundle:ConseillerRdp')
                ->findActifRdp($conseiller);
            $conseillerArray['rge'] = $rge;

            $conseillersArray[] = $conseillerArray;
            unset($conseillerArray);
        }

        // Create and configure the logger
        $logger = $this->get('logger');

        // ask the service for a Excel5
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("kentel")
            ->setLastModifiedBy("Cedric Thibault")
            ->setTitle("Rapport des prochaines fêtes de nos conseillers")
            ->setSubject("Liste des conseillers ayant bientôt leurs anniversaires")
            ->setDescription("Liste des conseillers et de leurs fêtes")
            ->setKeywords("conseillers kentel fêtes anniversaires")
            ->setCategory("Test result file");

        $phpExcelObject->getActiveSheet()->setTitle('Rapport_Fêtes');

        $em = $this->get('doctrine')->getManager();
        // $user = $this->getUser();

        $numligne = 1;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A' . $numligne, 'Conseiller')
            ->setCellValue('B' . $numligne, 'Affectation')
            ->setCellValue('C' . $numligne, 'RGE')
            ->setCellValue('D' . $numligne, 'Date de fête');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $phpExcelObject->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('f7f7f7');
        $phpExcelObject->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getStyle('A' . $numligne . ':D' . $numligne)->getFont()->setBold(true);
        $numligne++;

        foreach ($conseillersArray as $conseiller) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . $numligne, $conseiller['prenom'] . " " . $conseiller['nom']);
            if (empty($conseiller['affectations'])) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B' . $numligne, "Pas d'affectation");
            } else {
                $affectationsString = '';
                foreach ($conseiller['affectations'] as $affectation) {
                    $affectationsString .= $affectation->getMandat()->getClient()->getAcronyme() . ' ';
                }
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B' . $numligne, $affectationsString);
            }
            if (!empty($conseiller['rge'])) {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C' . $numligne, $conseiller['rge']->getRdp()->getPrenom() . ' ' . $conseiller['rge']->getRdp()->getNom());
            } else {
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C' . $numligne, 'Pas de RGE attribué');
            }

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D' . $numligne, $conseiller['dateFete']->format('d-M'));
            $numligne++;

        }
        // $phpExcelObject->setActiveSheetIndex(0)
            // ->setCellValue('A' . $numligne, '');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'rapportfetes.xls'
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
    //  Création d'un conseiller
    public function editmandatAction($id, Request $request)
    {
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('NurunRhBundle:Conseiller');
        $conseiller = $repository->find($id);

        if($conseiller->isDeleted()){
            throw new AccessDeniedException('Impossible de modifier un conseiller archivé.');
        }

        $conseillermandat = new ConseillerMandat();
        $conseillermandat->setConseiller($conseiller);
        $conseillermandat->setStatutAffectation('A');
        $conseillermandat->setPourcentage('100');
        $form = $this->get('form.factory')->create(new ConseillerMandatType(), $conseillermandat);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseillermandat);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Affectation bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $conseiller->getId())));
        }

        return $this->render('NurunRhBundle:Conseiller:editmandat.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        // On récupère le client $id
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);
        if($conseiller->isDeleted()){
            throw new AccessDeniedException('Impossible de modifier un conseiller archivé.');
        }

        $form = $this->get('form.factory')->create(new ConseillerType(), $conseiller);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($conseiller);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Conseiller bien enregistrée.');

            return $this->redirect($this->generateUrl('nurun_conseiller_view', array('id' => $conseiller->getId())));
        }

        return $this->render('NurunRhBundle:Conseiller:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function deleteAction($id, Request $request)
    {
        // On récupère le repository
        $em = $this->getDoctrine()->getManager();
        $em2 = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondante à l'id $id
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);

        // $conseiller est donc une instance de Nurun\RhBundle\Entity\Conseiller
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $conseiller) {
            throw new NotFoundHttpException("Le conseiller d'id " . $id . " n'existe pas.");
        }

        $mandats = $conseiller->getMandataires();
        foreach ($mandats as $mandat) {
            $mandat->setMandataire(null);
        }
        $em2->flush();

        $mandats = $conseiller->getChargeProjets();
        foreach ($mandats as $mandat) {
            $mandat->setChargeprojet(null);
        }        
        $em2->flush();

        $ressources = $em2->getRepository('NurunRhBundle:ConseillerRdp')->findByConseiller($conseiller);
        foreach ($ressources as $ressource) {
            $em2->remove($ressource);
        }
        $em2->flush();

        $rdps = $em2->getRepository('NurunRhBundle:ConseillerRdp')->findByRdp($conseiller);
        foreach ($rdps as $rdp) {
            $em2->remove($rdp);
        }
        $em2->flush();

        $affectations = $conseiller->getMandats();
        foreach ($affectations as $affectation) {
            $em2->remove($affectation);
        }
        $em2->flush();

        $em->remove($conseiller);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info', "Le conseiller a bien été supprimé.");

        return $this->redirect($this->generateUrl('nurun_conseiller_index'));
    }

    /**
     * @Security("has_role('ROLE_GESTION')")
     */
    public function restoreAction($id, Request $request)
    {
        // On récupère le repository
        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondante à l'id $id
        $conseiller = $em->getRepository('NurunRhBundle:Conseiller')->find($id);

        // $conseiller est donc une instance de Nurun\RhBundle\Entity\Conseiller
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $conseiller) {
            throw new NotFoundHttpException("Le conseiller d'id " . $id . " n'existe pas.");
        }

        $conseiller->restore();
        $em->flush();

        $request->getSession()->getFlashBag()->add('info', "Le conseiller a bien été restauré.");

        return $this->redirect($this->generateUrl('nurun_conseiller_index'));
    }


    public function menuAction()
    {
        // On récupère le repository
        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondante à l'id $id
        $conseillers = $em->getRepository('NurunRhBundle:Conseiller')->findLast(5);

        return $this->render('NurunRhBundle:Conseiller:menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listConseillers' => $conseillers
        ));
    }
}
