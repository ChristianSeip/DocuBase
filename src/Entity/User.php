<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(type: 'uuid', unique: true)]
	private ?UuidV4 $id = null;

	#[ORM\Column(length: 255, unique: true)]
	private ?string $username = null;

	#[ORM\Column(length: 255, unique: true, nullable: true)]
	private ?string $email = null;

	#[ORM\Column(length: 255)]
	private ?string $password = null;

	#[ORM\Column]
	private ?bool $is_verified = null;

	#[ORM\Column]
	private ?bool $is_locked = null;

	#[ORM\Column]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\ManyToMany(targetEntity: UserRole::class)]
	#[ORM\JoinTable(name: "user_role_assignments",
		joinColumns: [new ORM\JoinColumn(name: "user_id", referencedColumnName: "id")],
		inverseJoinColumns: [new ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
	)]
	private Collection $roles;

	public function __construct()
	{
		$this->id = Uuid::v4();
		$this->roles = new ArrayCollection();
		$this->created_at = new \DateTimeImmutable();
	}

	public function getId(): ?UuidV4
	{
		return $this->id;
	}

	public function getUserIdentifier(): string
	{
		return $this->email;
	}

	public function getUsername(): ?string
	{
		return $this->username;
	}

	public function setUsername(string $username): static
	{
		$this->username = $username;
		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(?string $email): static
	{
		$this->email = $email;
		return $this;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password): static
	{
		$this->password = $password;
		return $this;
	}

	public function getIsVerified(): ?bool
	{
		return $this->is_verified;
	}

	public function setIsVerified(bool $is_verified): static
	{
		$this->is_verified = $is_verified;
		return $this;
	}

	public function getIsLocked(): ?bool
	{
		return $this->is_locked;
	}

	public function setIsLocked(bool $is_locked): static
	{
		$this->is_locked = $is_locked;
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

	public function getRoles(): array
	{
		return $this->roles->map(function(UserRole $role) {
			return $role->getName();
		})->toArray();
	}

	public function getUserRoleEntities(): array
	{
		return $this->roles->toArray();
	}

	public function addRole(UserRole $role): self
	{
		if (!$this->roles->contains($role)) {
			$this->roles->add($role);
		}
		return $this;
	}

	public function removeRole(UserRole $role): self
	{
		$this->roles->removeElement($role);
		return $this;
	}

	public function clearRoles(): self
	{
		$this->roles->clear();
		return $this;
	}

	public function hasPermission(string $permissionName): bool
	{
		foreach ($this->roles as $role) {
			foreach ($role->getPermissions() as $permission) {
				if ($permission->getPermissionName() === $permissionName && $permission->getPermissionValue()) {
					return true;
				}
			}
		}
		return false;
	}

	public function eraseCredentials(): void
	{
		// If you store temporary sensitive data, clear it here
	}

	public function getSalt(): ?string
	{
		// Not needed if you're using bcrypt or argon2i
		return null;
	}
}
