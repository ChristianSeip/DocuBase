<?php

namespace App\Entity;

use App\Repository\UserRoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRoleRepository::class)]
class UserRole
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $description = null;

	#[ORM\Column]
	private ?bool $is_protected = false;

	#[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'role', cascade: ['remove'])]
	private Collection $permissions;

	#[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
	private Collection $users;

	public function __construct()
	{
		$this->users = new ArrayCollection();
		$this->permissions = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	public function isProtected(): ?bool
	{
		return $this->is_protected;
	}

	public function setProtected(bool $is_protected): static
	{
		$this->is_protected = $is_protected;

		return $this;
	}

	public function getPermissions(): Collection
	{
		return $this->permissions;
	}

	public function addPermission(Permission $permission): static
	{
		if (!$this->permissions->contains($permission)) {
			$this->permissions->add($permission);
			$permission->setRole($this);
		}

		return $this;
	}

	public function removePermission(Permission $permission): static
	{
		if ($this->permissions->removeElement($permission)) {
			if ($permission->getRole() === $this) {
				$permission->setRole(null);
			}
		}
		return $this;
	}

	public function setPermissionsFromArray(array $permissions): void
	{
		foreach ($permissions as $name => $value) {
			$permission = $this->permissions->filter(fn($p) => $p->getPermissionName() === $name)->first() ?? new Permission();
			$permission->setRole($this);
			$permission->setPermissionName($name);
			$permission->setPermissionValue($value);
			$this->addPermission($permission);
		}
	}

	public function getUsers(): Collection
	{
		return $this->users;
	}
}
