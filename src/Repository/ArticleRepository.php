<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Article::class);
	}

	/**
	 * Find all articles in a specific category
	 *
	 * @param int $categoryId ID of the category
	 * @return Article[] Array of articles in the specified category
	 */
	public function findArticlesByCategory(int $categoryId): array
	{
		return $this->createQueryBuilder('a')
			->andWhere('a.category = :categoryId')
			->setParameter('categoryId', $categoryId)
			->getQuery()
			->getResult();
	}

	/**
	 * Search for articles by a keyword in titles, texts, and category names
	 *
	 * @param string $keyword The keyword to search for
	 * @return Article[] Array of articles matching the keyword
	 */
	public function searchByKeyword(string $keyword): array
	{
		return $this->createQueryBuilder('a')
			->join('a.category', 'c')
			->andWhere('a.title LIKE :keyword OR a.text LIKE :keyword OR c.name LIKE :keyword')
			->setParameter('keyword', '%' . $keyword . '%')
			->getQuery()
			->getResult();
	}

	/**
	 * Search for articles by a keyword in a specific category
	 *
	 * @param string $keyword The keyword to search for
	 * @param int $categoryId ID of the category
	 * @return Article[] Array of articles matching the keyword in the specified category
	 */
	public function searchByKeywordAndCategoryId(string $keyword, int $categoryId): array
	{
		return $this->createQueryBuilder('a')
			->andWhere('a.category = :categoryId')
			->andWhere('a.title LIKE :keyword OR a.text LIKE :keyword')
			->setParameter('keyword', '%' . $keyword . '%')
			->setParameter('categoryId', $categoryId)
			->getQuery()
			->getResult();
	}

	/**
	 * Search for articles by a keyword in multiple categories
	 *
	 * @param string $keyword The keyword to search for
	 * @param array $categoryIds Array of category IDs to search in
	 * @return Article[] Array of articles matching the keyword in the specified categories
	 */
	public function searchByKeywordInCategories(string $keyword, array $categoryIds): array
	{
		return $this->createQueryBuilder('a')
			->andWhere('a.category IN (:categoryIds)')
			->andWhere('a.title LIKE :keyword OR a.text LIKE :keyword')
			->setParameter('keyword', '%' . $keyword . '%')
			->setParameter('categoryIds', $categoryIds)
			->getQuery()
			->getResult();
	}

	public function countNewArticles(int $days): int
	{
		return $this->createQueryBuilder('u')
			->select('COUNT(u.id)')
			->where('u.created_at >= :date')
			->setParameter('date', new \DateTimeImmutable("-$days days"))
			->getQuery()
			->getSingleScalarResult();
	}
}
