<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagementService
{
	private EntityManagerInterface $entityManager;
	private UserPasswordHasherInterface $passwordHasher;
	private LoggerInterface $logger;

	public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger)
	{
		$this->entityManager = $entityManager;
		$this->passwordHasher = $passwordHasher;
		$this->logger = $logger;
	}

	public function createUser(User $user, string $plainPassword): User
	{
		try {
			$hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
			$user->setPassword($hashedPassword);
			$user->setIsVerified(false);
			$user->setIsLocked(false);
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			return $user;
		}
		catch (Exception $e) {
			$this->logger->error('Error on creating new user: ' . $e->getMessage());
			throw $e;
		}
	}

	public function assignRole(User $user, int $roleId): void
	{
		try {
			$roleRepository = $this->entityManager->getRepository(UserRole::class);
			$userRole = $roleRepository->find($roleId);
			if (!$userRole) {
				throw new \Exception('Cannot find role with ID ' . $roleId);
			}
			$currentRoles = array_map(fn($role) => $role->getName(), $user->getRoles());
			if (!in_array($userRole->getName(), $currentRoles, true)) {
				$user->addRole($userRole);
				$this->entityManager->persist($user);
				$this->entityManager->flush();
			}
		} catch (\Exception $e) {
			$this->logger->error('Error assigning role to user: ' . $e->getMessage());
		}
	}
}
