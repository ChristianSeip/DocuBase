<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Security\HtmlSanitizer;
use App\Service\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
	private $articleRepository;
	private $slugger;
	private $entityManager;
	private $htmlSanitizer;
	private $permissionService;

	public function __construct(
		ArticleRepository $articleRepository,
		SluggerInterface $slugger,
		EntityManagerInterface $entityManager,
		HtmlSanitizer $htmlSanitizer,
		PermissionService $permissionService
	)
	{
		$this->articleRepository = $articleRepository;
		$this->slugger = $slugger;
		$this->entityManager = $entityManager;
		$this->htmlSanitizer = $htmlSanitizer;
		$this->permissionService = $permissionService;
	}

	#[Route('/article/{articleId}/{articleTitle}', name: 'app_article', requirements: ['articleId' => '\d+'])]
	public function articleDetail(int $articleId, string $articleTitle): Response
	{
		$article = $this->articleRepository->find($articleId);
		if (!$article) {
			throw $this->createNotFoundException('Article not found');
		}
		$slugTitle = $this->slugger->slug($article->getTitle())->lower()->toString();
		if ($articleTitle !== $slugTitle) {
			return $this->redirectToRoute('app_article', [
				'articleId' => $articleId,
				'articleTitle' => $slugTitle
			]);
		}
		return $this->render('article.html.twig', [
			'article' => $article,
		]);
	}

	#[Route('/article/new', name: 'app_article_create')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function createArticle(Request $request, CategoryRepository $categoryRepository): Response
	{
		if (!$this->permissionService->hasPermission('can_create_article') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$categories = $categoryRepository->findAll();
		$article = new Article();
		$form = $this->createForm(ArticleType::class, $article, ['categories' => $categories]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$article->setUser($this->getUser());
			$article->setText($this->htmlSanitizer->sanitize($article->getText()));
			$this->entityManager->persist($article);
			$this->entityManager->flush();
			return $this->redirectToRoute('app_article', [
				'articleId' => $article->getId(),
				'articleTitle' => $this->slugger->slug($article->getTitle())->lower()->toString(),
			]);
		}

		return $this->render('article-editor.html.twig', [
			'articleForm' => $form->createView(),
		]);
	}

	#[Route('/article/edit/{id}', name: 'app_article_edit')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function editArticle(Request $request, Article $article, CategoryRepository $categoryRepository): Response
	{
		if (!$this->permissionService->hasPermission('can_edit_article') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$categories = $categoryRepository->findAll();
		$form = $this->createForm(ArticleType::class, $article, ['categories' => $categories]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$article->setUpdatedAt(new \DateTimeImmutable());
			$article->setText($this->htmlSanitizer->sanitize($article->getText()));
			$this->entityManager->flush();
			return $this->redirectToRoute('app_article', [
				'articleId' => $article->getId(),
				'articleTitle' => $this->slugger->slug($article->getTitle())->lower()->toString(),
			]);
		}
		return $this->render('article-editor.html.twig', [
			'articleForm' => $form->createView(),
			'article' => $article,
		]);
	}

	#[Route('/article/delete/{id}', name: 'app_article_delete')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function deleteArticle(Request $request, Article $article): Response
	{
		if (!$this->permissionService->hasPermission('can_delete_article') || !$this->permissionService->isActive()) {
			throw $this->createAccessDeniedException();
		}
		$this->entityManager->remove($article);
		$this->entityManager->flush();
		$this->addFlash('success', 'Article deleted successfully');
		return $this->redirectToRoute('app_index');
	}
}
