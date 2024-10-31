<?php

namespace App\Entity;

use App\Repository\VerificationTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VerificationTokenRepository::class)]
class VerificationToken
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?User $user = null;

	#[ORM\Column(length: 255, unique: true)]
	private ?string $token = null;

	#[ORM\Column(nullable: true)]
	private ?\DateTimeImmutable $expires_at = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): static
	{
		$this->user = $user;
		return $this;
	}

	public function getToken(): ?string
	{
		return $this->token;
	}

	public function setToken(string $token): static
	{
		$this->token = $token;
		return $this;
	}

	public function getExpiresAt(): ?\DateTimeImmutable
	{
		return $this->expires_at;
	}

	public function setExpiresAt(\DateTimeImmutable $expires_at): static
	{
		$this->expires_at = $expires_at;
		return $this;
	}
}
