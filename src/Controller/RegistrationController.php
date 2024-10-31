<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\FormRequestLimiter;
use App\Service\MailService;
use App\Service\MailVerificationService;
use App\Service\UserManagementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
	#[Route('/register', name: 'app_register')]
	public function register(
		Request                 $request,
		UserManagementService   $userMgmtService,
		MailVerificationService $mailVerifyService,
		MailService             $mailService,
		TranslatorInterface     $translator,
		FormRequestLimiter			$formRequestLimiter,
	): Response
	{
		$user = new User();
		$form = $this->createForm(RegistrationType::class, $user);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if (!$formRequestLimiter->isRequestAllowed($request, 'register', 2, 120)) {
				$this->addFlash('error', $translator->trans('register_page.error.too_many_registrations'));
			}
			try {
				$user = $userMgmtService->createUser($user, $user->getPassword());
				$userMgmtService->assignRole($user, 2);
				$token = $mailVerifyService->generateToken($user, new \DateTimeImmutable('+24 hours'));
				if (!$token) {
					$this->addFlash('error', $translator->trans('email.verification.error.mail_sending_error'));
					return $this->redirectToRoute('app_register');
				}
				$mailService->sendMail($user, $translator->trans('email.verification.mail_subject'), $translator->trans('email.verification.mail_body', [
					'%username%' => $user->getUsername(),
					'%link%' =>  $this->generateUrl('app_verify_email', ['token' => $token->getToken()], URLGeneratorInterface::ABSOLUTE_URL)
				]));
				return $this->render('user-information.html.twig', [
					'title'   => $translator->trans('register_page.success.title'),
					'message' => $translator->trans('register_page.success.message'),
				]);
			}
			catch (\Exception $e) {
				$this->addFlash('error', $translator->trans('register_page.error.cannot_create_user') . $e->getMessage());
				return $this->redirectToRoute('app_register');
			}
		}
		return $this->render('register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}
}