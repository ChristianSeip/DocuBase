<?php
namespace App\Form;

use App\Entity\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminRoleType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, ['required' => true])
			->add('description', TextType::class, [
				'required' => false,
			]);

		foreach ($options['permissions'] as $permissionName => $permissionValue) {
			$builder->add($permissionName, CheckboxType::class, [
				'required' => false,
				'mapped' => false,
				'data' => $permissionValue ?? false,
			]);
		}
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults(['data_class' => UserRole::class, 'permissions' => []]);
	}
}