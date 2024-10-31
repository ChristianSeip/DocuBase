<?php
namespace App\Twig;

use App\Service\PermissionService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PermissionExtension extends AbstractExtension
{
	private $permissionService;

	public function __construct(PermissionService $permissionService)
	{
		$this->permissionService = $permissionService;
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('has_permission', [$this, 'hasPermission']),
			new TwigFunction('has_role', [$this, 'hasRole']),
		];
	}

	public function hasPermission(string $permissionName): bool
	{
		return $this->permissionService->hasPermission($permissionName);
	}

	public function hasRole(string $roleName): bool
	{
		return $this->permissionService->hasRole($roleName);
	}
}