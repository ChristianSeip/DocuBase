<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
	private MailerInterface $mailer;
	private string $fromEmail;
	private LoggerInterface $logger;

	public function __construct(MailerInterface $mailer, string $fromEmail, LoggerInterface $logger)
	{
		$this->mailer = $mailer;
		$this->fromEmail = $fromEmail;
		$this->logger = $logger;
	}

	/**
	 * Send a Mail to specific user.
	 *
	 * @param User   $user
	 * @param string $subject
	 * @param string $htmlText
	 *
	 * @return void
	 */
	public function sendMail(User $user, string $subject, string $htmlText): void
	{
		try {
			$email = (new Email())
				->from($this->fromEmail)
				->to($user->getEmail())
				->subject($subject)
				->html($htmlText);
			$this->mailer->send($email);
		}
		catch (TransportExceptionInterface $e) {
			$this->logger->error('Error sending email: ' . $e->getMessage());
			throw new \RuntimeException('Email sending failed, please try again later.');
		}
	}
}
