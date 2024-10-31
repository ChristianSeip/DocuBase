<?php

namespace App\Controller;

use App\Form\UserSettingsType;
use App\Service\MailService;
use App\Service\MailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserSettingsController extends AbstractController
{
	#[Route('/user/settings', name: 'app_user_settings')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function updateSettings(
		Request                     $request,
		EntityManagerInterface      $entityManager,
		UserPasswordHasherInterface $passwordHasher,
		MailVerificationService     $mailVerifyService,
		MailService                 $mailService,
		TranslatorInterface         $translator,
	): Response
	{
		$user = $this->getUser();
		if ($user->getIsLocked()) {
			$this->addFlash('error', $translator->trans('account_locked'));
			return $this->redirectToRoute('app_logout');
		}
		$form = $this->createForm(UserSettingsType::class, null, [
			'current_email' => $user->getEmail(),
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$newEmail = $form->get('email')->getData();
			if ($newEmail && $user->getEmail() !== $newEmail) {
				$user->setEmail($newEmail);
				$user->setIsVerified(false);
				$token = $mailVerifyService->generateToken($user, new \DateTimeImmutable('+24 hours'));
				if (!$token) {
					$this->addFlash('error', $translator->trans('email.verification.error.mail_sending_error'));
					return $this->redirectToRoute('app_register');
				}
				$mailService->sendMail($user, $translator->trans('email.verification.mail_subject'), $translator->trans('email.verification.mail_body', [
					'%username%' => $user->getUsername(),
					'%link%'     => $this->generateUrl('app_verify_email', ['token' => $token->getToken()], URLGeneratorInterface::ABSOLUTE_URL)
				]));
			}
			$newPassword = $form->get('plainPassword')->getData();
			if ($newPassword) {
				$hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
				$user->setPassword($hashedPassword);
			}
			$entityManager->persist($user);
			$entityManager->flush();
			$this->addFlash('success', $translator->trans('user_settings_page.success.message'));
			return $this->redirectToRoute('app_user_settings');
		}
		return $this->render('user_settings.html.twig', [
			'settingsForm' => $form->createView(),
		]);
	}
}