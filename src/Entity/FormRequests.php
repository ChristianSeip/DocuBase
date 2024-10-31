<?php

namespace App\Entity;

use App\Repository\FormRequestsRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a form request, typically used for spam prevention
 */
#[ORM\Entity(repositoryClass: FormRequestsRepository::class)]
class FormRequests
{
	/**
	 * The unique identifier of the form request.
	 */
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * The IP address from which the request originated.
	 */
	#[ORM\Column(length: 45)]
	private ?string $ip = null;

	/**
	 * The source identifier of the request, typically used for tracking.
	 */
	#[ORM\Column(length: 100)]
	private ?string $source = null;

	/**
	 * The timestamp indicating when the request was created.
	 */
	#[ORM\Column]
	private ?DateTimeImmutable $created_at = null;

	/**
	 * Initializes the created_at timestamp to the current date and time.
	 */
	public function __construct()
	{
		$this->created_at = new DateTimeImmutable();
	}

	/**
	 * Gets the unique identifier of the form request.
	 *
	 * @return int|null The form request ID.
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Gets the IP address from which the request originated.
	 *
	 * @return string|null The IP address.
	 */
	public function getIp(): ?string
	{
		return $this->ip;
	}

	/**
	 * Sets the IP address of the request.
	 *
	 * @param string $ip The IP address to set.
	 * @return static
	 */
	public function setIp(string $ip): static
	{
		$this->ip = $ip;

		return $this;
	}

	/**
	 * Gets the source identifier of the request.
	 *
	 * @return string|null The source identifier.
	 */
	public function getSource(): ?string
	{
		return $this->source;
	}

	/**
	 * Sets the source identifier of the request.
	 *
	 * @param string $source The source to set.
	 * @return self
	 */
	public function setSource(string $source): static
	{
		$this->source = $source;

		return $this;
	}

	/**
	 * Gets the creation timestamp of the request.
	 *
	 * @return DateTimeImmutable|null The creation date and time.
	 */
	public function getCreatedAt(): ?DateTimeImmutable
	{
		return $this->created_at;
	}

	/**
	 * Sets the creation timestamp of the request.
	 *
	 * @param DateTimeImmutable $created_at The timestamp to set.
	 *
	 * @return static
	 */
	public function setCreatedAt(DateTimeImmutable $created_at): static
	{
		$this->created_at = $created_at;

		return $this;
	}
}
