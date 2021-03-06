<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\False;


/**
 * ConseillerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConseillerRepository extends EntityRepository
{
    public function findAllActifs()
    {
        $qb = $this->createQueryBuilder('c');

        $qb->where('c.deletedAt is null');
        return $conseillers = $qb
            ->getQuery()
            ->getResult();
    }

    public function findOneFromText($conseillerText, $separateur)
    {
        $champs = explode($separateur, $conseillerText);

        $qb = $this->createQueryBuilder('c');

        $qb->where('c.prenom = :prenom')
            ->setParameter('prenom', $champs[0])
            ->andWhere('c.nom = :nom')
            ->setParameter('nom', $champs[1]);

        $conseiller = $qb
            ->getQuery()
            ->getOneOrNullResult();

        if (!$conseiller) {
            $qb = $this->createQueryBuilder('a');

            $qb->where('a.prenom = :prenom')
                ->setParameter('prenom', $champs[1])
                ->andWhere('a.nom = :nom')
                ->setParameter('nom', $champs[0]);

            $conseiller = $qb
                ->getQuery()
                ->getOneOrNullResult();
        }
        return $conseiller;
    }

    public function isEnglishSpoken($id)
    {
        // On précise le code EN pour english
        $code = 'EN';

        $qb = $this->createQueryBuilder('c');
        $qb->leftjoin('c.languages', 'n')
            ->leftjoin('n.language', 'l')
            ->where('c.id = :id')
            ->andWhere('l.code = :code')
            ->setParameter('id',$id)
            ->setParameter('code', $code);

        $result = $qb
            ->getQuery()
            ->getOneOrNullResult();

        if (empty($result)) {
            return False;
        }
        else return True;
    }

    public function englishLevelSpoken($id)
    {
        // On précise le code EN pour english
        $code = 'EN';

        $qb = $this->createQueryBuilder('c');
        $qb->select('c','j','n')
            ->leftjoin('c.languages', 'j')
            ->leftjoin('j.language', 'l')
            ->leftjoin('j.niveau', 'n')
            ->where('c.id = :id')
            ->andWhere('l.code = :code')
            ->setParameter('id',$id)
            ->setParameter('code', $code);

        $result = $qb
            ->getQuery()
            ->getArrayResult();

        if (empty($result)) {
            return null;
        }
        // On considère qu'il ne peut avoir qu'1 résultat car EN est unique et id aussi
        else return $result[0]['languages'][0]['niveau']['force'];
    }

    public function findByVP($vp)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->leftjoin('c.vicePresidence', 'v')
            ->where('v.acronyme = :vp')
            ->setParameter('vp', $vp)
            ->andWhere('c.deletedAt is null');
        return $conseillers = $qb
            ->getQuery()
            ->getResult();
    }

    public function findByVpExceptPigistes($vp)
    {
        $pige = 'Pigiste';
        $qb = $this->createQueryBuilder('c');

        $qb->leftjoin('c.vicePresidence', 'v')
            ->leftjoin('c.statut', 's')
            ->where('v.acronyme = :vp')
            ->andWhere('s.description != :pigiste')
            ->andWhere('c.deletedAt is null')
            ->setParameter('pigiste', $pige)
            ->setParameter('vp', $vp);
        return $conseillers = $qb
            ->getQuery()
            ->getResult();
    }

    public function findByProfil($profil)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->leftjoin('c.profil', 'p')
            ->where('p.profil = :profil')
            ->andWhere('c.deletedAt is null')
            ->setParameter('profil', $profil);
        return $conseillers = $qb
            ->getQuery()
            ->getResult();
    }

    public function findByProfilExceptPigistes($profil)
    {
        $pige = 'Pigiste';

        $qb = $this->createQueryBuilder('c');

        $qb->leftjoin('c.profil', 'p')
            ->leftjoin('c.statut', 's')
            ->where('p.id = :profil')
            ->andWhere('s.description != :pigiste')
            ->andWhere('c.deletedAt is null')
            ->setParameter('pigiste', $pige)
            ->setParameter('profil', $profil);
        return $conseillers = $qb
            ->getQuery()
            ->getResult();
    }

    public function listRegardingVp($vp)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.deletedAt is null');

        if ($vp != 'TOUT') {
            $qb->leftjoin('c.vicePresidence', 'v')
                ->andWhere('v.acronyme = :vp')
                ->setParameter('vp', $vp);
        }
        return $conseillers = $qb
            ->getQuery()
            ->getResult();
    }

    public function findOneByName($nom, $prenom)
    {

        $qb = $this->createQueryBuilder('c');

        $qb->where('c.prenom = :prenom')
            ->setParameter('prenom', $prenom)
            ->andWhere('c.nom = :nom')
            ->setParameter('nom', $nom);

        $conseiller = $qb
            ->getQuery()
            ->getOneOrNullResult();

        return $conseiller;
    }

    public function findLast($nombre)
    {
        $qb = $this->createQueryBuilder('c');
        // ->add('orderBy', 'u.name ASC');
        $qb->orderBy('c.dateArrivee', 'DESC');
        $conseillers = $qb
            ->getQuery()
            ->getResult();
        $i = 0;
        $result = array();

        if (count($conseillers) >= $nombre) {
            for ($i = 0; $i < $nombre; $i++) {
                $result[] = $conseillers[$i];
            }
        } else {
            $result = NULL;
        }
        return $result;
    }

    public function findWithMandatExpired()
    {
        $qb = $this->createQueryBuilder('a');

        // On fait une jointure avec l'entité Category avec pour alias « c »
        $qb
            ->join('a.mandats', 'm')
            ->addSelect('m')
            ->join('m.mandat', 'd')
            ->addSelect('d');
        $date = new \DateTime();

        // Puis on filtre sur le nom des catégories à l'aide d'un IN
        $qb->where('m.dateFin <= :date')
            ->setParameter('date', $date)
            ->orWhere('m.statutAffectation = :statut')
            ->setParameter('statut', 'I')
            ->orWhere('d.identifiant = :identifiant')
            ->setParameter('identifiant', 'Intermandat')
            ->andWhere('a.deletedAt is null');
        // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine
        // Enfin, on retourne le résultat
        return $qb
            ->getQuery()
            ->getArrayResult();
    }

    public function findByMandat($mandatId)
    {
        $qb = $this->createQueryBuilder('c');

        // On fait une jointure avec l'entité Conseiller avec pour alias « c »
        $qb
            ->join('c.mandats', 'm')
            ->addSelect('m');

        $qb->where('m.mandat = :mandatId')
            ->andWhere('m.deletedAt IS NULL')
            ->setParameter('mandatId', $mandatId);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findByMandatAffecte($mandatId)
    {
        $qb = $this->createQueryBuilder('c');
        $date = new \DateTime();

        // On fait une jointure avec l'entité Conseiller avec pour alias « c »
        $qb
            ->join('c.mandats', 'm')
            ->join('m.statutAffectation', 's')
            ->addSelect('s')
            ->addSelect('m');

        $qb->where('m.mandat = :mandatId')
            ->andWhere('m.deletedAt IS NULL')
            ->andWhere('s.acronyme = :statutAffecter')
            ->andWhere('m.dateFin >= :dateJour')
            ->andWhere('m.dateDebut <= :dateJour')
            ->setParameter('dateJour', $date)
            ->setParameter('mandatId', $mandatId)
            ->setParameter('statutAffecter', 'A');


        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findFetesAvenir($vp, $dateDeb, $dateFin)
    {

        $qb = $this->createQueryBuilder('c');
        $qb->where('c.deletedAt is null');

        if (!(($vp == 'VPOF') OR ($vp == 'TOUT'))) {
            $qb
                ->join('c.vicePresidence', 'v')
                ->addSelect('v')
                ->andWhere('v.acronyme = :vp')
                ->setParameter('vp', $vp);
        }

        $conseillers = $qb
            ->getQuery()
            ->getResult();

        $listconseillers = array();

        if (!empty($conseillers)) {
            foreach ($conseillers as $conseiller) {
                if (!empty($conseiller->getDateFete())) {

                    // On test si la plage calendaire est à cheval sur 2 années (décembre-janvier)
                    if ($dateDeb->format('md') > $dateFin->format('md')) {
                        // Si oui alors on test la période avant le 31/12 puis celle après le 01/01
                        if (($conseiller->getDateFete()->format('md') >= $dateDeb->format('md')) and ($conseiller->getDateFete()->format('md') <= '1231')) {
                            $listconseillers[] = $conseiller;
                        } else if (($conseiller->getDateFete()->format('md') <= $dateFin->format('md')) and ($conseiller->getDateFete()->format('md') >= '0101')) {
                            $listconseillers[] = $conseiller;
                        }
                    } //             Si la plage n'est pas à cheval alors on fait le test normal
                    else if (($conseiller->getDateFete()->format('md') >= $dateDeb->format('md')) and ($conseiller->getDateFete()->format('md') <= $dateFin->format('md'))) {

                        $listconseillers[] = $conseiller;
                    }
                }
            }

        }

        return $listconseillers;
    }


    public function findByMandatCoordination($mandat)
    {
        $em = $this->getEntityManager();
        $query=$em->createQuery("select c from NurunRhBundle:Conseiller c where :mandatId MEMBER OF c.mandatsCoordination")
            ->setParameter("mandatId", $mandat);
        $results=$query->getResult();
        return $results;
    }

    public function isCoordoonateurOfMandat($conseiller, $mandat)
    {
        $em = $this->getEntityManager();
        $query=$em->createQuery("select c from NurunRhBundle:Conseiller c where :mandatId MEMBER OF c.mandatsCoordination
              and c.id = :conseillerId")
            ->setParameter("mandatId", $mandat)
            ->setParameter("conseillerId", $conseiller);

        $results=$query->getOneOrNullResult();
        return $results;
    }
}
