<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function loadUserByIdentifier(string $identifier): UserInterface
	{
		$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $identifier]);
		if (!$user) {
			$exception = new UserNotFoundException();
			$exception->setUserIdentifier($identifier);
			throw $exception;
		}
		if ($user->getIsLocked()) {
			throw new CustomUserMessageAuthenticationException('account_locked');
		}
		if (!$user->getIsVerified()) {
			throw new CustomUserMessageAuthenticationException('login_page.error.email_not_verified');
		}
		return $user;
	}

	public function refreshUser(UserInterface $user): UserInterface
	{
		return $this->loadUserByIdentifier($user->getEmail());
	}

	public function supportsClass(string $class): bool
	{
		return $class === User::class || is_subclass_of($class, User::class);
	}
}
