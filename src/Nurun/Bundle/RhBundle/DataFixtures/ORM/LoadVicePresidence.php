<?php
// src/Nurun/RhBundle/DataFixtures/ORM/LoadVicePresidence.php
namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nurun\RhBundle\Entity\VicePresidence;

/**
 * Description of LoadVicePresidence
 *
 * @author cedric
 */
class LoadVicePresidence implements FixtureInterface 
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
// Liste des noms de catégorie à ajouter
    $vps = array(
        array("VPTS", "Vice-présidence Technologies et Sécurité"),
        array("VPSI", "Vice-présidence Solutions informatiques"),
        array("VPAS", "Vice-présidence Affaires et Stratégie")
    );

    foreach ($vps as $vp) {
      // On crée le client
      $vicePresidence = new \Nurun\Bundle\RhBundle\Entity\VicePresidence();
      $vicePresidence->setDescription($vp[1]);
      $vicePresidence->setAcronyme($vp[0]);
      $manager->persist($vicePresidence);
      
    }

    // On déclenche l'enregistrement de toutes les status chargés
    $manager->flush();
  }}
