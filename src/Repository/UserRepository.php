<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}

	public function countNewUsers(int $days): int
	{
		return $this->createQueryBuilder('u')
			->select('COUNT(u.id)')
			->where('u.created_at >= :date')
			->setParameter('date', new \DateTimeImmutable("-$days days"))
			->getQuery()
			->getSingleScalarResult();
	}

	public function countActiveUsers(): int
	{
		return $this->createQueryBuilder('u')
			->select('COUNT(u.id)')
			->where('u.is_locked = false')
			->getQuery()
			->getSingleScalarResult();
	}

	public function countBlockedUsers(): int
	{
		return $this->createQueryBuilder('u')
			->select('COUNT(u.id)')
			->where('u.is_locked = true')
			->getQuery()
			->getSingleScalarResult();
	}
}
