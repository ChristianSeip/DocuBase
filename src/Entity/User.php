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

/**
 * Represents a user in the system, including identification, authentication,
 * roles, and permissions.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	/**
	 * The unique identifier (UUID) of the user.
	 */
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(type: 'uuid', unique: true)]
	private ?UuidV4 $id = null;

	/**
	 * The username of the user.
	 */
	#[ORM\Column(length: 255, unique: true)]
	private ?string $username = null;

	/**
	 * The email address of the user.
	 */
	#[ORM\Column(length: 255, unique: true, nullable: true)]
	private ?string $email = null;

	/**
	 * The password hash of the user.
	 */
	#[ORM\Column(length: 255)]
	private ?string $password = null;

	/**
	 * The verification status of the user.
	 */
	#[ORM\Column]
	private ?bool $is_verified = null;

	/**
	 * The lock status of the user account.
	 */
	#[ORM\Column]
	private ?bool $is_locked = null;

	/**
	 * The account creation date.
	 */
	#[ORM\Column]
	private ?\DateTimeImmutable $created_at = null;

	/**
	 * The roles assigned to the user.
	 */
	#[ORM\ManyToMany(targetEntity: UserRole::class)]
	#[ORM\JoinTable(name: "user_role_assignments",
		joinColumns: [new ORM\JoinColumn(name: "user_id", referencedColumnName: "id")],
		inverseJoinColumns: [new ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
	)]
	private Collection $roles;

	/**
	 * User constructor. Initializes a UUID and sets the creation date.
	 */
	public function __construct()
	{
		$this->id = Uuid::v4();
		$this->roles = new ArrayCollection();
		$this->created_at = new \DateTimeImmutable();
	}

	/**
	 * Gets the unique identifier (UUID) of the user.
	 *
	 * @return UuidV4|null The user UUID.
	 */
	public function getId(): ?UuidV4
	{
		return $this->id;
	}

	/**
	 * Gets the unique identifier used for authentication.
	 *
	 * @return string The user identifier (email).
	 */
	public function getUserIdentifier(): string
	{
		return $this->email;
	}

	/**
	 * Gets the username of the user.
	 *
	 * @return string|null The username.
	 */
	public function getUsername(): ?string
	{
		return $this->username;
	}

	/**
	 * Sets the username for the user.
	 *
	 * @param string $username The username to set.
	 *
	 * @return self
	 */
	public function setUsername(string $username): static
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * Gets the email address of the user.
	 *
	 * @return string|null The user email.
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * Sets the email address for the user.
	 *
	 * @param string|null $email The email to set.
	 *
	 * @return self
	 */
	public function setEmail(?string $email): static
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * Gets the password hash of the user.
	 *
	 * @return string|null The password hash.
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}

	/**
	 * Sets the password hash for the user.
	 *
	 * @param string $password The hashed password to set.
	 *
	 * @return static
	 */
	public function setPassword(string $password): static
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * Checks if the user is verified.
	 *
	 * @return bool|null True if verified, false otherwise.
	 */
	public function getIsVerified(): ?bool
	{
		return $this->is_verified;
	}

	/**
	 * Sets the verification status of the user.
	 *
	 * @param bool $is_verified The verification status to set.
	 *
	 * @return static
	 */
	public function setIsVerified(bool $is_verified): static
	{
		$this->is_verified = $is_verified;
		return $this;
	}

	/**
	 * Checks if the user account is locked.
	 *
	 * @return bool|null True if locked, false otherwise.
	 */
	public function getIsLocked(): ?bool
	{
		return $this->is_locked;
	}

	/**
	 * Sets the lock status of the user account.
	 *
	 * @param bool $is_locked The lock status to set.
	 *
	 * @return static
	 */
	public function setIsLocked(bool $is_locked): static
	{
		$this->is_locked = $is_locked;
		return $this;
	}

	/**
	 * Gets the account creation date and time.
	 *
	 * @return \DateTimeImmutable|null The account creation date.
	 */
	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	/**
	 * Sets the account creation date and time.
	 *
	 * @param \DateTimeImmutable $created_at The creation date to set.
	 *
	 * @return static
	 */
	public function setCreatedAt(\DateTimeImmutable $created_at): static
	{
		$this->created_at = $created_at;
		return $this;
	}

	/**
	 * Gets the roles of the user as an array of role names.
	 *
	 * @return array The roles assigned to the user.
	 */
	public function getRoles(): array
	{
		return $this->roles->map(function(UserRole $role) {
			return $role->getName();
		})->toArray();
	}

	/**
	 * Gets the roles of the user as an array of UserRole entities.
	 *
	 * @return array The roles assigned to the user as UserRole entities.
	 */
	public function getUserRoleEntities(): array
	{
		return $this->roles->toArray();
	}

	/**
	 * Adds a role to the user.
	 *
	 * @param UserRole $role The role to add.
	 *
	 * @return static
	 */
	public function addRole(UserRole $role): static
	{
		if (!$this->roles->contains($role)) {
			$this->roles->add($role);
		}
		return $this;
	}

	/**
	 * Removes a role from the user.
	 *
	 * @param UserRole $role The role to remove.
	 *
	 * @return static
	 */
	public function removeRole(UserRole $role): static
	{
		$this->roles->removeElement($role);
		return $this;
	}

	/**
	 * Clears all roles from the user.
	 *
	 * @return static
	 */
	public function clearRoles(): static
	{
		$this->roles->clear();
		return $this;
	}

	/**
	 * Checks if the user has a specific permission.
	 *
	 * @param string $permissionName The permission to check.
	 *
	 * @return bool True if the user has the permission, false otherwise.
	 */
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
