<?php

namespace App\Entity;

use App\Repository\UserRoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a user role within the system, including associated permissions and whether the role is protected.
 */
#[ORM\Entity(repositoryClass: UserRoleRepository::class)]
class UserRole
{
	/**
	 * The unique identifier for the user role.
	 */
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * The name of the user role.
	 */
	#[ORM\Column(length: 255)]
	private ?string $name = null;

	/**
	 * A description of the user role.
	 */
	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $description = null;

	/**
	 * Indicates if the role is protected from deletion or modification.
	 */
	#[ORM\Column]
	private ?bool $is_protected = false;

	/**
	 * The permissions assigned to this role.
	 */
	#[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'role', cascade: ['remove'])]
	private Collection $permissions;

	/**
	 * The users associated with this role.
	 */
	#[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
	private Collection $users;

	/**
	 * UserRole constructor. Initializes permissions and user collections.
	 */
	public function __construct()
	{
		$this->users = new ArrayCollection();
		$this->permissions = new ArrayCollection();
	}

	/**
	 * Gets the unique identifier for the role.
	 *
	 * @return int|null The role ID.
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Gets the name of the role.
	 *
	 * @return string|null The role name.
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * Sets the name for the role.
	 *
	 * @param string $name The name to set for the role.
	 *
	 * @return static
	 */
	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Gets the description of the role.
	 *
	 * @return string|null The role description.
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * Sets the description for the role.
	 *
	 * @param string|null $description The description to set.
	 *
	 * @return static
	 */
	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Checks if the role is protected.
	 *
	 * @return bool|null True if protected, false otherwise.
	 */
	public function isProtected(): ?bool
	{
		return $this->is_protected;
	}

	/**
	 * Sets the protected status of the role.
	 *
	 * @param bool $is_protected The protected status to set.
	 *
	 * @return static
	 */
	public function setProtected(bool $is_protected): static
	{
		$this->is_protected = $is_protected;

		return $this;
	}

	/**
	 * Gets the collection of permissions associated with the role.
	 *
	 * @return Collection The permissions.
	 */
	public function getPermissions(): Collection
	{
		return $this->permissions;
	}

	/**
	 * Adds a permission to the role.
	 *
	 * @param Permission $permission The permission to add.
	 *
	 * @return static
	 */
	public function addPermission(Permission $permission): static
	{
		if (!$this->permissions->contains($permission)) {
			$this->permissions->add($permission);
			$permission->setRole($this);
		}

		return $this;
	}

	/**
	 * Removes a permission from the role.
	 *
	 * @param Permission $permission The permission to remove.
	 *
	 * @return static
	 */
	public function removePermission(Permission $permission): static
	{
		if ($this->permissions->removeElement($permission)) {
			if ($permission->getRole() === $this) {
				$permission->setRole(null);
			}
		}
		return $this;
	}

	/**
	 * Sets permissions from an associative array, where the key is the permission name
	 * and the value is the permission's enabled status.
	 *
	 * @param array $permissions Array of permission names and values.
	 */
	public function setPermissionsFromArray(array $permissions): void
	{
		foreach ($permissions as $name => $value) {
			$permission = $this->permissions->filter(fn ($p) => $p->getPermissionName() === $name)->first() ?? new Permission();
			$permission->setRole($this);
			$permission->setPermissionName($name);
			$permission->setPermissionValue($value);
			$this->addPermission($permission);
		}
	}

	/**
	 * Gets the collection of users assigned to the role.
	 *
	 * @return Collection The users associated with the role.
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}
}
