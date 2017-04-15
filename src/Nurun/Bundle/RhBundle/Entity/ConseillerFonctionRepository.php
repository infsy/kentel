<?php

namespace Nurun\Bundle\RhBundle\Entity;

/**
 * ConseillerFonctionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConseillerFonctionRepository extends \Doctrine\ORM\EntityRepository
{
	public function getByConseillerFonction($conseiller, $fonction)
	{	
		$qb = $this
			->createQueryBuilder('cf')
			->where('cf.fonction = :fonction')
			->setParameter('fonction', $fonction)
			->andWhere('cf.conseiller = :conseiller')
			->setParameter('conseiller', $conseiller)
		;

		return $qb
			->getQuery()
			->getResult()
		;
	}
}
