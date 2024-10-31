<?php
namespace App\Form;

use App\Entity\User;
use App\Entity\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserCreationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('username', TextType::class, [
				'required' => true,
			])
			->add('email', EmailType::class, [
				'required' => true,
			])
			->add('password', PasswordType::class, [
				'required' => true,
			])
			->add('roles', EntityType::class, [
				'class' => UserRole::class,
				'choice_label' => 'name',
				'multiple' => true,
				'expanded' => false,
				'required' => false,
			])
			->add('is_verified', CheckboxType::class, [
				'required' => false,
			])
			->add('is_locked', CheckboxType::class, [
				'required' => false,
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
