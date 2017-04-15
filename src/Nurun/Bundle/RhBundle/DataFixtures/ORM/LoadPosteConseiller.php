<?php
// src/Nurun/RhBundle/DataFixtures/ORM/LoadPosteConseiller.php
namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nurun\RhBundle\Entity\PosteConseiller;

/**
 * Description of LoadPosteConseiller
 *
 * @author cedric
 */
class LoadPosteConseiller implements FixtureInterface 
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
      
// Liste des noms de catégorie à ajouter
    $postes = array(
        array("Administrateur de systèmes 1"),
        array("Administrateur de systèmes 2"),
        array("Conseillèr(e) en analyse sécurité 1"),
        array("Conseillèr(e) en analyse sécurité 2"),
        array("Conseillèr(e) en analyse sécurité et techno 1"),
        array("Conseillèr(e) en analyse sécurité et techno 2"),
        array("Conseillèr(e) en analyse sécurité et techno 3"),
        array("Conseillèr(e) en analyse technologique 1"),
        array("Conseillèr(e) en analyse technologique 3"),
        array("Conseillèr(e) en architecture sécurité 2"),
        array("Conseillèr(e) en architecture sécurité 3"),
        array("Conseillèr(e) en architecture sécurité techno 2"),
        array("Conseillèr(e) en architecture sécurité techno 3"),
        array("Conseillèr(e) en architecture technologique"),
        array("Conseillèr(e) en architecture technologique 1"),
        array("Conseillèr(e) en architecture technologique 2"),
        array("Conseillèr(e) en architecture technologique 3"),
        array("Conseillèr(e) en gestion de projet TI"),
        array("Conseillèr(e) support à la gestion de projet (PCO)"),
        array("Coordonnateur au soutien technique"),
        array("Coordonnateur technique"),
        array("Rédacteur(trice) technique"),
        array("Spécialiste en gouvernance sécurité 2"),
        array("Spécialiste en gouvernance sécurité 3"),
        array("Technicien(ne) niv. 1"),
        array("Technicien(ne) niv. 2"),
        array("Technicien(ne) niv. 3")
    );

    foreach ($postes as $poste) {
      // On crée le client
      $posteConseiller = new \Nurun\Bundle\RhBundle\Entity\PosteConseiller();
      $posteConseiller->setDescription($poste[0]);
      $manager->persist($posteConseiller);
      
    }

    // On déclenche l'enregistrement de toutes les status chargés
    $manager->flush();
  }}
