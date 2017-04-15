<?php
// src/Nurun/RhBundle/DataFixtures/ORM/LoadStatutConseiller.php
namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nurun\RhBundle\Entity\StatutConseiller;

/**
 * Description of LoadStatutConseiller
 *
 * @author cedric
 */
class LoadStatutConseiller implements FixtureInterface 
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
// Liste des noms de catégorie à ajouter
    $status = array(
        array("Permanent"),
        array("Contractuel"),
        array("Pigiste")
    );

    foreach ($status as $statut) {
      // On crée le client
      $statutConseiller = new \Nurun\Bundle\RhBundle\Entity\StatutConseiller();
      $statutConseiller->setDescription($statut[0]);
      $manager->persist($statutConseiller);
      
    }

    // On déclenche l'enregistrement de toutes les status chargés
    $manager->flush();
  }}
