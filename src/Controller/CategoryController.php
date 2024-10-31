<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
	private $categoryRepository;
	private $articleRepository;
	private $slugger;

	public function __construct(CategoryRepository $categoryRepository, ArticleRepository $articleRepository, SluggerInterface $slugger)
	{
		$this->categoryRepository = $categoryRepository;
		$this->articleRepository = $articleRepository;
		$this->slugger = $slugger;
	}

	#[Route('/category/{categoryId}/{categoryName}', name: 'app_category',  requirements: ['categoryId' => '\d+'])]
	public function categoryOverview(int $categoryId, string $categoryName)
	{
		$category = $this->categoryRepository->find($categoryId);
		if (!$category) {
			throw $this->createNotFoundException('Category not found');
		}
		$slugTitle = $this->slugger->slug($category->getName())->lower()->toString();
		if ($categoryName !== $slugTitle) {
			return $this->redirectToRoute('app_category', [
				'categoryId' => $categoryId,
				'categoryName' => $slugTitle
			]);
		}
		$articles = $this->articleRepository->findArticlesByCategory($category->getId());
		$subcategories = $this->categoryRepository->findSubcategories($category->getId());
		return $this->render('category.html.twig', [
			'category'      => $category,
			'articles'      => $articles,
			'subcategories' => $subcategories,
		]);
	}
}