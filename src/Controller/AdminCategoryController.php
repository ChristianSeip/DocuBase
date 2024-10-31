<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\AdminCategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Service\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminCategoryController extends AbstractController
{
	private CategoryRepository $categoryRepository;
	private ArticleRepository $articleRepository;
	private PermissionService $permissionService;
	private EntityManagerInterface $entityManager;
	private TranslatorInterface $translator;

	public function __construct(
		CategoryRepository     $categoryRepository,
		ArticleRepository      $articleRepository,
		PermissionService      $permissionService,
		EntityManagerInterface $entityManager,
		TranslatorInterface    $translator
	)
	{
		$this->categoryRepository = $categoryRepository;
		$this->articleRepository = $articleRepository;
		$this->permissionService = $permissionService;
		$this->entityManager = $entityManager;
		$this->translator = $translator;
	}

	#[Route('/acp/categories', name: 'acp_category_list')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function list(CategoryRepository $categoryRepository): Response
	{
		if (!$this->permissionService->hasPermission('can_use_acp') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$categories = $categoryRepository->findAll();
		$mainCategories = [];
		$subCategories = [];
		foreach ($categories as $category) {
			if ($category->getParent() === null) {
				$mainCategories[$category->getId()] = [
					'category'      => $category,
					'subcategories' => []
				];
			}
			else {
				$subCategories[$category->getParent()->getId()][] = $category;
			}
		}
		return $this->render('acp/category-list.html.twig', [
			'mainCategories' => $mainCategories,
			'subCategories'  => $subCategories,
		]);
	}

	#[Route('/acp/category/new', name: 'acp_category_create')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function createCategory(Request $request, EntityManagerInterface $entityManager): Response
	{
		if (!$this->permissionService->hasPermission('can_create_categories') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$category = new Category();
		$form = $this->createForm(AdminCategoryType::class, $category, [
			'parent_categories' => $this->categoryRepository->findTopLevelCategories()
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($category);
			$entityManager->flush();
			$this->addFlash('success', $this->translator->trans('acp_category_create_page.success_message'));
			return $this->redirectToRoute('acp_category_list');
		}
		return $this->render('acp/category-form.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route('/acp/category/edit/{id}', name: 'acp_category_edit')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function editCategory(Category $category, Request $request): Response
	{
		if (!$this->permissionService->hasPermission('can_edit_categories') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$form = $this->createForm(AdminCategoryType::class, $category, [
			'parent_categories' => array_filter($this->categoryRepository->findTopLevelCategories(), fn ($parentCategory) => $parentCategory->getId() !== $category->getId())
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->entityManager->flush();
			$this->addFlash('success', $this->translator->trans('acp_category_edit_page.success_message'));
			return $this->redirectToRoute('acp_category_list');
		}
		return $this->render('acp/category-form.html.twig', [
			'form'     => $form->createView(),
			'category' => $category
		]);
	}

	#[Route('/acp/category/delete/{id}', name: 'acp_category_delete')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function deleteCategory(Category $category, Request $request): Response
	{
		if (!$this->permissionService->hasPermission('can_delete_categories') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		if ($category->getParent() === null && !$this->isCategoryEmpty($category)) {
			$this->addFlash('error', $this->translator->trans('acp_category_delete_page.category_not_empty'));
			return $this->redirectToRoute('acp_category_list');
		}
		$this->entityManager->beginTransaction();
		try {
			$parentCategory = $category->getParent();
			if ($parentCategory !== null) {
				$articles = $this->articleRepository->findBy(['category' => $category]);
				foreach ($articles as $article) {
					$article->setCategory($parentCategory);
				}
				$this->entityManager->flush();
			}
			$this->entityManager->remove($category);
			$this->entityManager->flush();
			$this->entityManager->commit();
			$this->addFlash('success', $this->translator->trans('acp_category_delete_page.category_deleted'));
		}
		catch (\Exception $e) {
			$this->entityManager->rollback();
			$this->addFlash('error', $this->translator->trans('acp_category_delete_page.deletion_failed'));
		}
		return $this->redirectToRoute('acp_category_list');
	}

	private function isCategoryEmpty(Category $category): bool
	{
		$subcategories = $this->categoryRepository->findSubcategories($category->getId());
		$articleCount = $this->articleRepository->count(['category' => $category]);
		return empty($subcategories) && $articleCount === 0;
	}
}