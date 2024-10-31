<?php
namespace App\Controller;

use App\Entity\Permission;
use App\Entity\UserRole;
use App\Form\AdminRoleType;
use App\Repository\UserRoleRepository;
use App\Service\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminRoleController extends AbstractController
{
	private UserRoleRepository $roleRepository;
	private PermissionService $permissionService;
	private EntityManagerInterface $entityManager;
	private TranslatorInterface $translator;

	public function __construct(
		UserRoleRepository $UserRoleRepository,
		PermissionService $permissionService,
		EntityManagerInterface $entityManager,
		TranslatorInterface $translator
	)
	{
		$this->roleRepository = $UserRoleRepository;
		$this->permissionService = $permissionService;
		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}

	#[Route('/acp/roles', name: 'acp_role_list')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function listRoles(): Response
	{
		if (!$this->permissionService->hasPermission('can_use_acp') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$roles = $this->roleRepository->findAll();
		return $this->render('acp/role-list.html.twig', [
			'roles' => $roles,
		]);
	}

	#[Route('/acp/role/new', name: 'acp_role_create')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function createRole(Request $request): Response
	{
		if (!$this->permissionService->hasPermission('can_create_roles') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$role = new UserRole();
		$adminRole = $this->roleRepository->findOneBy(['name' => 'Admin']);
		$permissions = [];
		if ($adminRole) {
			foreach ($adminRole->getPermissions() as $permission) {
				$permissions[$permission->getPermissionName()] = false;
			}
		}
		$form = $this->createForm(AdminRoleType::class, $role, ['permissions' => $permissions]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->entityManager->persist($role);
			$this->entityManager->flush();
			foreach ($permissions as $permissionName => $defaultValue) {
				$permission = new Permission();
				$permission->setRole($role);
				$permission->setPermissionName($permissionName);
				$permission->setPermissionValue($form->get($permissionName)->getData() ?? false);
				$this->entityManager->persist($permission);
			}
			$this->entityManager->flush();
			$this->addFlash('success', $this->translator->trans('acp_role_create_page.success_message'));
			return $this->redirectToRoute('acp_role_list');
		}
		return $this->render('acp/role-form.html.twig', [
			'form' => $form->createView(),
			'is_edit' => false,
		]);
	}

	#[Route('/acp/role/edit/{id}', name: 'acp_role_edit')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function editRole(UserRole $role, Request $request): Response
	{
		if (!$this->permissionService->hasPermission('can_edit_roles') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$permissions = [];
		foreach ($role->getPermissions() as $permission) {
			$permissions[$permission->getPermissionName()] = $permission->getPermissionValue();
		}
		$form = $this->createForm(AdminRoleType::class, $role, [
			'permissions' => $permissions,
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			foreach ($permissions as $permissionName => $permissionValue) {
				$permission = $role->getPermissions()->filter(fn($perm) => $perm->getPermissionName() === $permissionName)->first();
				if ($permission) {
					$permission->setPermissionValue($form->get($permissionName)->getData() ?? false);
				}
			}
			$this->entityManager->flush();
			$this->addFlash('success', $this->translator->trans('acp_role_edit_page.success_message'));
			return $this->redirectToRoute('acp_role_list');
		}
		return $this->render('acp/role-form.html.twig', [
			'form' => $form->createView(),
			'is_edit' => true,
		]);
	}

	#[Route('/acp/role/delete/{id}', name: 'acp_role_delete')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function deleteRole(UserRole $role, Request $request): Response
	{
		if (!$this->permissionService->hasPermission('can_delete_roles') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		if ($role->isProtected()) {
			$this->addFlash('error', $this->translator->trans('acp_role_delete_page.cannot_delete_protected_role'));
			return $this->redirectToRoute('acp_role_list');
		}
		foreach ($role->getPermissions() as $permission) {
			$this->entityManager->remove($permission);
		}
		$this->entityManager->remove($role);
		$this->entityManager->flush();
		$this->addFlash('success', $this->translator->trans('acp_role_delete_page.role_deleted'));
		return $this->redirectToRoute('acp_role_list');
	}

}