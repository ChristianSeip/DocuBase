<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(targetEntity: UserRole::class, inversedBy: 'permissions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?UserRole $role = null;

	#[ORM\Column(length: 255)]
	private ?string $permission_name = null;

	#[ORM\Column(type: 'boolean')]
	private ?bool $permission_value = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getRole(): ?UserRole
	{
		return $this->role;
	}

	public function setRole(?UserRole $role): static
	{
		$this->role = $role;

		return $this;
	}

	public function getPermissionName(): ?string
	{
		return $this->permission_name;
	}

	public function setPermissionName(string $permission_name): static
	{
		$this->permission_name = $permission_name;

		return $this;
	}

	public function getPermissionValue(): ?bool
	{
		return $this->permission_value;
	}

	public function setPermissionValue(bool $permission_value): static
	{
		$this->permission_value = $permission_value;

		return $this;
	}
}
