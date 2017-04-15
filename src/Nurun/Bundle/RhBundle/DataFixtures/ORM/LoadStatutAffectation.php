<?php
// src/Nurun/RhBundle/DataFixtures/ORM/LoadStatutAffectation.php
namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nurun\RhBundle\Entity\StatutAffectation;

/**
 * Description of LoadStatutAffectation
 *
 * @author cedric
 */
class LoadStatutAffectation implements FixtureInterface 
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
// Liste des noms de catégorie à ajouter
    $status = array(
        array("Affecté", "A"),
        array("Proposé", "P"),
        array("Proposé Stratégique", "PS"),
        array("Proposé appel d'offres", "P(AO)"),
        array("Proposé stratégique appel d'offfres", "PS(AO)")
    );

    foreach ($status as $statut) {
      // On crée le client
      $statutAffectation = new \Nurun\Bundle\RhBundle\Entity\StatutAffectation();
      $statutAffectation->setDescription($statut[0]);
      $statutAffectation->setAcronyme($statut[1]);
      $manager->persist($statutAffectation);
      
    }

    // On déclenche l'enregistrement de toutes les status chargés
    $manager->flush();
  }}
