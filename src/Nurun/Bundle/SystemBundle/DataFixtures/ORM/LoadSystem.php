<?php
// src/Nurun/RhBundle/DataFixtures/ORM/LoadStatutConseiller.php
namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nurun\SystemBundle\Entity\System;

/**
 * Description of LoadSystem
 *
 * @author cedric
 */
class LoadSystem implements FixtureInterface 
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    
      $system = new \Nurun\Bundle\SystemBundle\Entity\System();
      $system->setEmailVpas('tony.duchesne@nurun.com');
      $system->setEmailVpts('cedric.thibault@nurun.com');
      $system->setEmailVpsi('');
      $system->setEmailAdmin('administration-qc@nurun.com');

      $manager->persist($system);
      

    // On déclenche l'enregistrement de toutes les variables chargées
    $manager->flush();
  }}
