<?php
namespace App\Controller;

use App\Service\MailVerificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailVerifyController extends AbstractController
{
	#[Route('/verify-email', name: 'app_verify_email')]
	public function verifyEmail(Request $request, MailVerificationService $mailVerificationService, TranslatorInterface $translator): Response
	{
		$token = $request->query->get('token');
		$verificationToken = $mailVerificationService->validateToken($token);
		if (!$verificationToken) {
			return $this->render('user-information.html.twig', [
				'title' => $translator->trans('email.verification.error.invalid_token_title'),
				'message' => $translator->trans('email.verification.error.invalid_token_message')
			]);
		}
		$mailVerificationService->markUserAsVerified($verificationToken);
		return $this->render('user-information.html.twig', [
			'title' => $translator->trans('email.verification.success.title'),
			'message' => $translator->trans('email.verification.success.message')
		]);
	}
}