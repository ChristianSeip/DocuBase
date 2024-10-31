<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\ManyToOne(targetEntity: self::class)]
	#[ORM\JoinColumn(onDelete: 'SET NULL')]
	private ?self $parent = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getParent(): ?self
	{
		return $this->parent;
	}

	public function setParent(?self $parent): self
	{
		$this->parent = $parent;
		return $this;
	}
}