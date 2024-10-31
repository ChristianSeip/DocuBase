<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a permission assigned to a specific user role, including a permission name
 * and value indicating whether the permission is granted.
 */
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
	/**
	 * The unique identifier of the permission.
	 */
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * The user role associated with this permission.
	 */
	#[ORM\ManyToOne(targetEntity: UserRole::class, inversedBy: 'permissions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?UserRole $role = null;

	/**
	 * The name of the permission (e.g., "can_create_article").
	 */
	#[ORM\Column(length: 255)]
	private ?string $permission_name = null;

	/**
	 * The value of the permission indicating whether it is granted (true or false).
	 */
	#[ORM\Column(type: 'boolean')]
	private ?bool $permission_value = null;

	/**
	 * Gets the unique identifier of the permission.
	 *
	 * @return int|null The permission ID.
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Gets the user role associated with this permission.
	 *
	 * @return UserRole|null The associated user role.
	 */
	public function getRole(): ?UserRole
	{
		return $this->role;
	}

	/**
	 * Sets the user role associated with this permission.
	 *
	 * @param UserRole|null $role The user role to associate.
	 *
	 * @return static
	 */
	public function setRole(?UserRole $role): static
	{
		$this->role = $role;

		return $this;
	}

	/**
	 * Gets the name of the permission.
	 *
	 * @return string|null The permission name.
	 */
	public function getPermissionName(): ?string
	{
		return $this->permission_name;
	}

	/**
	 * Sets the name of the permission.
	 *
	 * @param string $permission_name The name to set.
	 *
	 * @return static
	 */
	public function setPermissionName(string $permission_name): static
	{
		$this->permission_name = $permission_name;

		return $this;
	}

	/**
	 * Gets the value of the permission indicating if it is granted.
	 *
	 * @return bool|null The permission value (true or false).
	 */
	public function getPermissionValue(): ?bool
	{
		return $this->permission_value;
	}

	/**
	 * Sets the value of the permission indicating if it is granted.
	 *
	 * @param bool $permission_value The permission value to set.
	 *
	 * @return static
	 */
	public function setPermissionValue(bool $permission_value): static
	{
		$this->permission_value = $permission_value;

		return $this;
	}
}
