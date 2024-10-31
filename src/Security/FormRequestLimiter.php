<?php

namespace App\Security;

use App\Entity\FormRequests;
use App\Repository\FormRequestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class FormRequestLimiter
{
	private FormRequestsRepository $formRequestsRepository;
	private EntityManagerInterface $entityManager;

	public function __construct(FormRequestsRepository $formRequestsRepository, EntityManagerInterface $entityManager)
	{
		$this->formRequestsRepository = $formRequestsRepository;
		$this->entityManager = $entityManager;
	}

	public function isRequestAllowed(Request $request, string $source, int $requestLimit = 2, int $timeLimit = 45): bool
	{
		$ip = $request->getClientIp();
		$now = new \DateTimeImmutable();
		$timeThreshold = $now->modify('-' . $timeLimit . ' seconds');
		$requestCount = $this->formRequestsRepository->countRequests($ip, $source, $timeThreshold);
		if ($requestCount >= $requestLimit) {
			return false;
		}
		$this->addEntry($ip, $source);
		return true;
	}

	private function addEntry($ip, $source): void
	{
		$formRequest = new FormRequests();
		$formRequest->setIp($ip);
		$formRequest->setSource($source);
		$this->entityManager->persist($formRequest);
		$this->entityManager->flush();
	}
}
