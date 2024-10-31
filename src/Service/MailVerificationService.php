<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\VerificationToken;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MailVerificationService
{
	private EntityManagerInterface $entityManager;
	private LoggerInterface $logger;

	public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
	{
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}

	/**
	 * Generate a verification token
	 *
	 * @param User                    $user
	 * @param DateTimeImmutable $expiresAt
	 *
	 * @return VerificationToken|null
	 */
	public function generateToken(User $user, DateTimeImmutable $expiresAt): ?VerificationToken
	{
		try {
			$token = new VerificationToken();
			$token->setUser($user);
			$token->setToken(bin2hex(random_bytes(32)));
			$token->setExpiresAt($expiresAt);
			$this->entityManager->persist($token);
			$this->entityManager->flush();
			return $token;
		}
		catch (\Exception $e) {
			$this->logger->error('A verification token could not be created.: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Validate users token.
	 *
	 * @param string $token
	 *
	 * @return VerificationToken|null
	 */
	public function validateToken(string $token): ?VerificationToken
	{
		try {
			$repository = $this->entityManager->getRepository(VerificationToken::class);
			$verificationToken = $repository->findOneBy(['token' => $token]);
			if (!$verificationToken || ($verificationToken->getExpiresAt() && $verificationToken->getExpiresAt() < new \DateTimeImmutable())) {
				return null;
			}
			return $verificationToken;
		}
		catch (\Exception $e) {
			$this->logger->error('The token could not be verified: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Mark token and user as verified.
	 *
	 * @param VerificationToken $verificationToken
	 *
	 * @return void
	 */
	public function markUserAsVerified(VerificationToken $verificationToken): void
	{
		try {
			$user = $verificationToken->getUser();
			$user->setIsVerified(true);
			$this->entityManager->remove($verificationToken);
			$this->entityManager->flush();
		}
		catch (\Exception $e) {
			$this->logger->error('The user could not be marked as verified:  ' . $e->getMessage());
		}
	}
}
