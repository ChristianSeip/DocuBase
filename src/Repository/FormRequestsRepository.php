<?php

namespace App\Repository;

use App\Entity\FormRequests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormRequests>
 */
class FormRequestsRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, FormRequests::class);
	}

	public function countRequests(string $ip, string $source, \DateTimeImmutable $timeThreshold): int
	{
		try {
			return (int)$this->createQueryBuilder('f')
				->select('COUNT(f.id)')
				->where('f.ip = :ip')
				->andWhere('f.source = :source')
				->andWhere('f.created_at >= :timeThreshold')
				->setParameter('ip', $ip)
				->setParameter('source', $source)
				->setParameter('timeThreshold', $timeThreshold)
				->getQuery()
				->getSingleScalarResult();
		}
		catch (NonUniqueResultException $e) {
			return 0;
		}
	}
}