<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSettingsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, [
				'label' => 'user_settings_page.form.email_label',
				'required' => true,
				'data' => $options['current_email'],
			])
			->add('plainPassword', RepeatedType::class, [
				'type' => PasswordType::class,
				'first_options' => ['label' => 'user_settings_page.form.password_label'],
				'second_options' => ['label' => 'user_settings_page.form.password_repeat_label'],
				'required' => false,
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'current_email' => null,
		]);
	}
}
