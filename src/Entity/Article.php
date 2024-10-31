<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;

/**
 * Represents an article in the system, associated with a category and an optional user.
 */
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{

	/**
	 * The unique identifier for the article.
	 */
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * The category to which the article belongs.
	 */
	#[ORM\ManyToOne(targetEntity: Category::class)]
	#[ORM\JoinColumn(nullable: false)]
	private ?Category $category = null;

	/**
	 * The user who created or owns the article, nullable.
	 */
	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(onDelete: 'SET NULL')]
	private ?User $user = null;

	/**
	 * Title of the article.
	 */
	#[ORM\Column(length: 255)]
	private ?string $title = null;

	/**
	 * Main content of the article.
	 */
	#[ORM\Column(type: 'text')]
	private ?string $text = null;

	/**
	 * The date and time when the article was created.
	 */
	#[ORM\Column]
	private ?DateTimeImmutable $created_at = null;

	/**
	 * The date and time when the article was last updated, nullable.
	 */
	#[ORM\Column(nullable: true)]
	private ?DateTimeImmutable $updated_at = null;

	/**
	 * Initializes the article with the current date and time as the creation timestamp.
	 */
	public function __construct()
	{
		$this->created_at = new DateTimeImmutable();
	}

	/**
	 * Gets the article's unique identifier.
	 *
	 * @return int|null The article ID.
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Gets the category to which the article belongs.
	 *
	 * @return Category|null The associated category.
	 */
	public function getCategory(): ?Category
	{
		return $this->category;
	}

	/**
	 * Sets the category of the article.
	 *
	 * @param Category|null $category The category to associate with the article.
	 *
	 * @return static
	 */
	public function setCategory(?Category $category): static
	{
		$this->category = $category;
		return $this;
	}

	/**
	 * Gets the user who created or owns the article.
	 *
	 * @return User|null The associated user.
	 */
	public function getUser(): ?User
	{
		return $this->user;
	}

	/**
	 * Sets the user who created or owns the article.
	 *
	 * @param User|null $user The user to associate with the article.
	 *
	 * @return static
	 */
	public function setUser(?User $user): static
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * Gets the title of the article.
	 *
	 * @return string|null The article title.
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * Sets the title of the article.
	 *
	 * @param string $title The title to set.
	 *
	 * @return static
	 */
	public function setTitle(string $title): static
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Gets the main content of the article.
	 *
	 * @return string|null The article content.
	 */
	public function getText(): ?string
	{
		return $this->text;
	}

	/**
	 * Sets the main content of the article.
	 *
	 * @param string $text The content to set.
	 *
	 * @return static
	 */
	public function setText(string $text): static
	{
		$this->text = $text;
		return $this;
	}

	/**
	 * Gets the creation date and time of the article.
	 *
	 * @return DateTimeImmutable|null The creation timestamp.
	 */
	public function getCreatedAt(): ?DateTimeImmutable
	{
		return $this->created_at;
	}

	/**
	 * Gets the last update date and time of the article.
	 *
	 * @return DateTimeImmutable|null The update timestamp.
	 */
	public function getUpdatedAt(): ?DateTimeImmutable
	{
		return $this->updated_at;
	}

	/**
	 * Sets the date and time when the article was last updated.
	 *
	 * @param DateTimeImmutable|null $updated_at The update timestamp.
	 *
	 * @return static
	 */
	public function setUpdatedAt(?DateTimeImmutable $updated_at): static
	{
		$this->updated_at = $updated_at;
		return $this;
	}
}
