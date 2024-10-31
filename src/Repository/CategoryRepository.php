<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Category::class);
	}

	/**
	 * Find all top-level categories (those without a parent)
	 *
	 * @return Category[] Array of top-level categories
	 */
	public function findTopLevelCategories(): array
	{
		return $this->createQueryBuilder('c')
			->where('c.parent IS NULL')
			->getQuery()
			->getResult();
	}

	/**
	 * Find subcategories for a given parent category by parent ID
	 *
	 * @param int $parentId ID of the parent category
	 * @return Category[] Array of subcategories
	 */
	public function findSubcategories(int $parentId): array
	{
		return $this->createQueryBuilder('c')
			->where('c.parent = :parentId')
			->setParameter('parentId', $parentId)
			->getQuery()
			->getResult();
	}

	/**
	 * Find a category by its name
	 *
	 * @param string $name Name of the category
	 * @return Category|null The found category or null if not found
	 */
	public function findByName(string $name): ?Category
	{
		return $this->createQueryBuilder('c')
			->andWhere('c.name = :name')
			->setParameter('name', $name)
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * Find subcategories for a parent category by the parent's name
	 *
	 * @param string $parentName Name of the parent category
	 * @return Category[] Array of subcategories
	 */
	public function findSubcategoriesByParentName(string $parentName): array
	{
		$parentCategory = $this->findByName($parentName);
		if ($parentCategory) {
			return $this->createQueryBuilder('c')
				->andWhere('c.parent = :parentId')
				->setParameter('parentId', $parentCategory->getId())
				->getQuery()
				->getResult();
		}
		return [];
	}

	/**
	 * Find all relevant categories (parent and subcategories) by category name
	 *
	 * @param string $categoryName Name of the parent category
	 * @return Category[] Array of parent category and its subcategories
	 */
	public function findAllRelevantCategoriesByName(string $categoryName): array
	{
		$parentCategory = $this->findByName($categoryName);
		if ($parentCategory) {
			return $this->createQueryBuilder('c')
				->andWhere('c.id = :parentId OR c.parent = :parentId')
				->setParameter('parentId', $parentCategory->getId())
				->getQuery()
				->getResult();
		}
		return [];
	}

	/**
	 * Find a specific subcategory by its name under a given parent category
	 *
	 * @param string $subcategoryName Name of the subcategory
	 * @param string $parentName Name of the parent category
	 * @return Category|null The subcategory found or null if not found
	 */
	public function findSubcategoryByNameAndParent(string $subcategoryName, string $parentName): ?Category
	{
		$parentCategory = $this->findByName($parentName);
		if ($parentCategory) {
			return $this->createQueryBuilder('c')
				->andWhere('c.name = :subcategoryName')
				->andWhere('c.parent = :parentId')
				->setParameter('subcategoryName', $subcategoryName)
				->setParameter('parentId', $parentCategory->getId())
				->getQuery()
				->getOneOrNullResult();
		}
		return null;
	}
}