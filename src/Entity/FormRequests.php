<?php

namespace App\Entity;

use App\Repository\FormRequestsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormRequestsRepository::class)]
class FormRequests
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 45)]
	private ?string $ip = null;

	#[ORM\Column(length: 100)]
	private ?string $source = null;

	#[ORM\Column]
	private ?\DateTimeImmutable $created_at = null;

	public function __construct()
	{
		$this->created_at = new \DateTimeImmutable();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getIp(): ?string
	{
		return $this->ip;
	}

	public function setIp(string $ip): static
	{
		$this->ip = $ip;

		return $this;
	}

	public function getSource(): ?string
	{
		return $this->source;
	}

	public function setSource(string $source): static
	{
		$this->source = $source;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	public function setCreatedAt(\DateTimeImmutable $created_at): static
	{
		$this->created_at = $created_at;

		return $this;
	}
}
