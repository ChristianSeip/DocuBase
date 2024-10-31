<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$categories = $this->buildCategoryTree($options['categories']);
		$builder
			->add('title', TextType::class)
			->add('category', EntityType::class, [
				'class' => Category::class,
				'choices' => $categories,
				'choice_label' => function (Category $category) {
					$prefix = $this->getCategoryPrefix($category);
					return $prefix . $category->getName();
				},
				'placeholder' => 'Choose a Category',
			])
			->add('text', TextareaType::class, [
				'attr' => ['id' => 'article_text'],
				'required' => false
			]);
	}

	/**
	 * Build a flat list of categories in a parent/child structure.
	 *
	 * @param Category[] $categories
	 * @param Category|null $parent
	 * @param int $level
	 * @return Category[]
	 */
	private function buildCategoryTree(array $categories, Category $parent = null, int $level = 0): array
	{
		$result = [];
		foreach ($categories as $category) {
			if ($category->getParent() === $parent) {
				$result[] = $category;
				$children = $this->buildCategoryTree($categories, $category, $level + 1);
				$result = array_merge($result, $children);
			}
		}
		return $result;
	}

	/**
	 * Create a visual prefix for child categories.
	 *
	 * @param Category $category
	 * @return string
	 */
	private function getCategoryPrefix(Category $category): string
	{
		$level = 0;
		$parent = $category->getParent();
		while ($parent) {
			$level++;
			$parent = $parent->getParent();
		}
		return str_repeat('— ', $level);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Article::class,
			'categories' => [],
		]);
	}
}
