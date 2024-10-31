<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;

/**
 * Represents a category that can have subcategories and be assigned as a parent category.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
	/**
	 * The unique identifier for the category.
	 */
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * The name of the category.
	 */
	#[ORM\Column(length: 255)]
	private ?string $name = null;

	/**
	 * The parent category, nullable. Used for hierarchical organization.
	 */
	#[ORM\ManyToOne(targetEntity: self::class)]
	#[ORM\JoinColumn(onDelete: 'SET NULL')]
	private ?self $parent = null;

	/**
	 * Gets the unique identifier of the category.
	 *
	 * @return int|null The category ID.
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Gets the name of the category.
	 *
	 * @return string|null The category name.
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * Sets the name of the category.
	 *
	 * @param string $name The name to set.
	 *
	 * @return static
	 */
	public function setName(string $name): static
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Gets the parent category, if it exists.
	 *
	 * @return Category|null The parent category.
	 */
	public function getParent(): ?Category
	{
		return $this->parent;
	}

	/**
	 * Sets the parent category.
	 *
	 * @param Category|null $parent The parent category to set.
	 *
	 * @return static
	 */
	public function setParent(?self $parent): static
	{
		$this->parent = $parent;
		return $this;
	}
}