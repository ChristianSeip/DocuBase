<?php
namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRoleRepository;
use App\Service\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminDashboardController extends AbstractController
{
	private UserRepository $userRepository;
	private ArticleRepository $articleRepository;
	private CategoryRepository $categoryRepository;
	private UserRoleRepository $userRoleRepository;
	private PermissionService $permissionService;

	public function __construct(
		UserRepository     $userRepository,
		ArticleRepository  $articleRepository,
		CategoryRepository $categoryRepository,
		UserRoleRepository $userRoleRepository,
		PermissionService $permissionService
	)
	{
		$this->userRepository = $userRepository;
		$this->articleRepository = $articleRepository;
		$this->categoryRepository = $categoryRepository;
		$this->userRoleRepository = $userRoleRepository;
		$this->permissionService = $permissionService;
	}

	#[Route('/acp', name: 'acp_dashboard')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function index(): Response
	{
		if (!$this->permissionService->hasPermission('can_use_acp') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$totalUsers = $this->userRepository->count([]);
		$newUsersLast30Days = $this->userRepository->countNewUsers(30);
		$blockedUsers = $this->userRepository->countBlockedUsers();
		$totalArticles = $this->articleRepository->count([]);
		$newArticlesLast30Days = $this->articleRepository->countNewArticles(30);
		$totalCategories = $this->categoryRepository->count([]);
		$totalRoles = $this->userRoleRepository->count([]);
		$phpVersion = phpversion();
		$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown Server';
		return $this->render('acp/dashboard.html.twig', [
			'totalUsers'            => $totalUsers,
			'newUsersLast30Days'    => $newUsersLast30Days,
			'blockedUsers'          => $blockedUsers,
			'totalArticles'         => $totalArticles,
			'newArticlesLast30Days' => $newArticlesLast30Days,
			'totalCategories'       => $totalCategories,
			'totalRoles'            => $totalRoles,
			'phpVersion' => $phpVersion,
			'serverSoftware' => $serverSoftware,
		]);
	}
}
