<?php
namespace App\Controller;

use App\Security\FormRequestLimiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthController extends AbstractController
{

	private FormRequestLimiter $formRequestLimiter;

	public function __construct(FormRequestLimiter $formRequestLimiter)
	{
		$this->formRequestLimiter = $formRequestLimiter;
	}

	#[Route('/login', name: 'app_login')]
	public function login(Request $request, AuthenticationUtils $authenticationUtils, TranslatorInterface $translator,): Response
	{
		if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
			return $this->redirectToRoute('app_index');
		}
		if ($request->isMethod('POST')) {
			if (!$this->formRequestLimiter->isRequestAllowed($request, 'login', 5, 180)) {
				$this->addFlash('error', $translator->trans('login_page.error.too_many_attempts'));
			}
		}
		$lastUsername = $authenticationUtils->getLastUsername();
		$error = $authenticationUtils->getLastAuthenticationError();
		return $this->render('login.html.twig', [
			'last_username' => $lastUsername,
			'error' => $error,
		]);
	}

	#[Route('/logout', name: 'app_logout')]
	public function logout(): void
	{
		throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall.');
	}
}