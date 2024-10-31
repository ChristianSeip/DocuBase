<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use App\Entity\User;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(targetEntity: Category::class)]
	#[ORM\JoinColumn(nullable: false)]
	private ?Category $category = null;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(onDelete: 'SET NULL')]
	private ?User $user = null;

	#[ORM\Column(length: 255)]
	private ?string $title = null;

	#[ORM\Column(type: 'text')]
	private ?string $text = null;

	#[ORM\Column]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(nullable: true)]
	private ?\DateTimeImmutable $updated_at = null;

	public function __construct()
	{
		$this->created_at = new \DateTimeImmutable();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getCategory(): ?Category
	{
		return $this->category;
	}

	public function setCategory(?Category $category): self
	{
		$this->category = $category;
		return $this;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;
		return $this;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;
		return $this;
	}

	public function getText(): ?string
	{
		return $this->text;
	}

	public function setText(string $text): self
	{
		$this->text = $text;
		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updated_at;
	}

	public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
	{
		$this->updated_at = $updated_at;
		return $this;
	}
}
