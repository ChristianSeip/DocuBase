<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PermissionService
{
	private Security $security;
	private EntityManagerInterface $entityManager;

	private $user;

	public function __construct(Security $security, EntityManagerInterface $entityManager)
	{
		$this->security = $security;
		$this->entityManager = $entityManager;
		$this->user = $this->security->getUser();
		if (!$this->user) {
			$this->user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'Guest']);
		}
	}

	public function hasPermission(string $permissionName): bool
	{
		return $this->user->hasPermission($permissionName);
	}

	public function hasRole(string $roleName): bool
	{
		return in_array($roleName, $this->user->getRoles());
	}

	public function isActive(): bool
	{
		return !$this->user->getIsLocked() && $this->user->getIsVerified();
	}
}