<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Security\FormRequestLimiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
{
	private $categoryRepository;
	private $articleRepository;

	public function __construct(CategoryRepository $categoryRepository, ArticleRepository $articleRepository)
	{
		$this->categoryRepository = $categoryRepository;
		$this->articleRepository = $articleRepository;
	}

	#[Route('/', name: 'app_index')]
	public function index()
	{
		return $this->render('index.html.twig', [
			'articles' => []
		]);
	}

	#[Route('/search', name: 'app_search', methods: ['GET'])]
	public function search(Request $request, FormRequestLimiter $formRequestLimiter, TranslatorInterface $translator)
	{
		if (!$formRequestLimiter->isRequestAllowed($request, 'search', 5, 60)) {
			$this->addFlash('error', $translator->trans('too_many_search_requests'));
		}
		$searchQuery = $request->query->get('q');
		$articles = [];
		$parts = array_map('trim', explode('/', $searchQuery));
		$numParts = count($parts);
		switch ($numParts) {
			case 3:
				[$category, $subcategory, $keyword] = $parts;
				$subcategoryEntity = $this->categoryRepository->findSubcategoryByNameAndParent($subcategory, $category);
				if ($subcategoryEntity) {
					$articles = $this->articleRepository->searchByKeywordAndCategoryId($keyword, $subcategoryEntity->getId());
				}
				break;
			case 2:
				[$category, $keyword] = $parts;
				$relevantCategories = $this->categoryRepository->findAllRelevantCategoriesByName($category);
				if (!empty($relevantCategories)) {
					$categoryIds = array_column($relevantCategories, 'id');
					$articles = $this->articleRepository->searchByKeywordInCategories($keyword, $categoryIds);
				}
				break;
			case 1:
				$keyword = $parts[0];
				$articles = $this->articleRepository->searchByKeyword($keyword);
				break;
		}
		return $this->render('index.html.twig', [
			'articles' => $articles,
			'searchQuery' => $searchQuery,
		]);
	}

}