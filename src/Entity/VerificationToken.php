<?php

namespace App\Entity;

use App\Repository\VerificationTokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a verification token used for user account verification.
 */
#[ORM\Entity(repositoryClass: VerificationTokenRepository::class)]
class VerificationToken
{
	/**
	 * The unique identifier for the verification token.
	 */
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * The user associated with the verification token.
	 */
	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?User $user = null;

	/**
	 * The unique verification token string.
	 */
	#[ORM\Column(length: 255, unique: true)]
	private ?string $token = null;

	/**
	 * The expiration date and time of the token.
	 */
	#[ORM\Column(nullable: true)]
	private ?DateTimeImmutable $expires_at = null;

	/**
	 * Gets the unique identifier of the verification token.
	 *
	 * @return int|null The token ID.
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Gets the user associated with the verification token.
	 *
	 * @return User|null The associated user.
	 */
	public function getUser(): ?User
	{
		return $this->user;
	}

	/**
	 * Sets the user associated with the verification token.
	 *
	 * @param User|null $user The user to associate with the token.
	 *
	 * @return static
	 */
	public function setUser(?User $user): static
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * Gets the token string.
	 *
	 * @return string|null The token.
	 */
	public function getToken(): ?string
	{
		return $this->token;
	}

	/**
	 * Sets the token string.
	 *
	 * @param string $token The token to set.
	 *
	 * @return static
	 */
	public function setToken(string $token): static
	{
		$this->token = $token;
		return $this;
	}

	/**
	 * Gets the expiration date and time of the token.
	 *
	 * @return DateTimeImmutable|null The expiration date and time.
	 */
	public function getExpiresAt(): ?DateTimeImmutable
	{
		return $this->expires_at;
	}

	/**
	 * Sets the expiration date and time of the token.
	 *
	 * @param DateTimeImmutable $expires_at The expiration date and time.
	 *
	 * @return static
	 */
	public function setExpiresAt(DateTimeImmutable $expires_at): static
	{
		$this->expires_at = $expires_at;
		return $this;
	}
}
