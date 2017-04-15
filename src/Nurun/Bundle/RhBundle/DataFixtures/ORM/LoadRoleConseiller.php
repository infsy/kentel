<?php
// src/Nurun/RhBundle/DataFixtures/ORM/LoadRoleConseiller.php
namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nurun\RhBundle\Entity\RoleConseiller;

/**
 * Description of LoadRoleConseiller
 *
 * @author cedric
 */
class LoadRoleConseiller implements FixtureInterface 
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
      
// Liste des noms de catégorie à ajouter
    $roles = array(
        array("Conseiller"),
        array("Conseiller expert"),
        array("Conseillère"),
        array("Directeur"),
        array("Directeur exécutif associé"),
        array("Vice-président")
    );

    foreach ($roles as $role) {
      // On crée le client
      $roleConseiller = new \Nurun\Bundle\RhBundle\Entity\RoleConseiller();
      $roleConseiller->setDescription($role[0]);
      $manager->persist($roleConseiller);
      
    }

    // On déclenche l'enregistrement de toutes les status chargés
    $manager->flush();
  }}
