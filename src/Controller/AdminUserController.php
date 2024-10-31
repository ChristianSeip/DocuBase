<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserCreationType;
use App\Form\AdminUserSettingsType;
use App\Repository\UserRepository;
use App\Service\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminUserController extends AbstractController
{
	private PermissionService $permissionService;
	private EntityManagerInterface $entityManager;
	private TranslatorInterface $translator;

	public function __construct(PermissionService $permissionService, EntityManagerInterface $entityManager, TranslatorInterface $translator)
	{
		$this->permissionService = $permissionService;
		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}

	#[Route('/acp/users', name: 'acp_user_list')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function listUsers(UserRepository $userRepository)
	{
		if (!$this->permissionService->hasPermission('can_edit_users') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$users = $userRepository->createQueryBuilder('u')
			->where('u.username != :guest')
			->setParameter('guest', 'Guest')
			->getQuery()
			->getResult();
		return $this->render('acp/user-list.html.twig', [
			'users' => $users,
		]);
	}

	#[Route('/acp/user/edit/{id}', name: 'acp_user_edit')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function editUser(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
	{
		if (!$this->permissionService->hasPermission('can_edit_users') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$form = $this->createForm(AdminUserSettingsType::class, $user, [
			'current_roles' => $user->getUserRoleEntities(),
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$user->setIsVerified($form->get('is_verified')->getData());
			$user->setIsLocked($form->get('is_locked')->getData());
			$plainPassword = $form->get('plainPassword')->getData();
			if ($plainPassword) {
				$hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
				$user->setPassword($hashedPassword);
			}
			$user->clearRoles();
			$selectedRoles = $form->get('roles')->getData();
			foreach ($selectedRoles as $role) {
				$user->addRole($role);
			}
			$this->entityManager->flush();
			return $this->redirectToRoute('acp_user_list');
		}
		return $this->render('acp/user-edit.html.twig', [
			'editForm' => $form->createView(),
		]);
	}

	#[Route('/acp/user/delete/{id}', name: 'acp_user_delete')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function deleteUser(Request $request, User $user, ): Response
	{
		if (!$this->permissionService->hasPermission('can_delete_users') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		if (in_array('Admin', $user->getRoles(), true)) {
			$this->addFlash('error', $this->translator->trans('acp_user_delete.error_admin_role', ['%username' => $user->getUsername()]));
			return $this->redirectToRoute('acp_user_list');
		}
		$this->entityManager->remove($user);
		$this->entityManager->flush();
		$this->addFlash('success', $this->translator->trans('acp_user_delete.success_message', ['%username' => $user->getUsername()]));
		return $this->redirectToRoute('acp_user_list');
	}

	#[Route('/acp/user/new', name: 'acp_user_create')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response
	{
		if (!$this->permissionService->hasPermission('can_create_users') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$user = new User();
		$form = $this->createForm(AdminUserCreationType::class, $user);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
			$user->setPassword($hashedPassword);
			foreach ($form->get('roles')->getData() as $role) {
				$user->addRole($role);
			}
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			$this->addFlash('success', $this->translator->trans('acp_user_create_page.success_message'));
			return $this->redirectToRoute('acp_user_list');
		}
		return $this->render('acp/user-create.html.twig', [
			'userForm' => $form->createView(),
		]);
	}
}