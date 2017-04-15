<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MandatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MandatRepository extends EntityRepository
{
     public function findOneFromText($mandatText, $separateur)
    {
     $champs = explode($separateur, $mandatText);
     
     $qb = $this->createQueryBuilder('c');

      $qb->where('c.identifiant = :identifiant')
           ->setParameter('identifiant', $champs[0])
      ;

  $mandat=$qb
    ->getQuery()
    ->getOneOrNullResult()
  ;
  
  if (empty($mandat))
  {
      $qb = $this->createQueryBuilder('a');

       $qb->where('a.identifiant = :identifiant')
           ->setParameter('identifiant', $champs[1])
      ;
      
    $mandat=$qb
        ->getQuery()
        ->getOneOrNullResult()
    ;
  }
  
  return $mandat;
}

  public function findByMandataireOrChargeProjet($conseiller)
  {
    $qb = $this->createQueryBuilder('m');
 
    $qb->where('m.mandataire = :conseiller OR m.chargeprojet = :conseiller')
      ->setParameter('conseiller', $conseiller)
      ->orderBy('m.identifiant', 'ASC');
    ;
       
    $conseillerRdp=$qb
      ->getQuery()
      ->getResult()
    ;

    return $conseillerRdp;
  }

  public function findActives()
  {
    $qb = $this->createQueryBuilder('m');
    $qb ->where('m.deletedAt is null');

    $liste = $qb->getQuery()->getResult();

    return $liste;
  }

    public function findByAdresse($adresse)
    {
//        $qb = $this->createQueryBuilder('a');
        $em = $this->getEntityManager();
        $query=$em->createQuery("select m from NurunRhBundle:Mandat m where :adresseId MEMBER OF m.adresses
              AND m.deletedAt is NULL")
            ->setParameter("adresseId", $adresse);
        $results=$query->getResult();
        return $results;
    }

    public function findByAdresseAndClient($adresse,$client)
    {
//        $qb = $this->createQueryBuilder('a');
        $em = $this->getEntityManager();
        $query=$em->createQuery("select m from NurunRhBundle:Mandat m JOIN m.client c
            where :adresseId MEMBER OF m.adresses AND c.id = :clientId
              AND m.deletedAt is NULL")
            ->setParameter("adresseId", $adresse)
            ->setParameter("clientId", $client);

        $results=$query->getResult();
        return $results;
    }
}