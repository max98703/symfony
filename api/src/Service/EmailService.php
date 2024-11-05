<?php

namespace Api\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * This service class was a wrapper of Email service
 */
class EmailService
{

    /**
     * @var MailerInterface $mailer
     */
    private MailerInterface $mailer;

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }



    /**
     * @param $recipients
     * @param $text
     * @param $subject
     * @param string $from
     * @return void
     */
    public function sendEmail($recipients, $text = null, $subject = null, string $from = 'maxrai788@gmail.com'): void
    {
        $_recipients = is_array($recipients) ? $recipients : [$recipients];

        try {

            $email = (new Email())
                ->from($from)
                ->to(...$_recipients)
                ->subject($subject)
                ->text($text);

            $this->mailer->send($email);

        } catch (TransportExceptionInterface $exception) {
            $this->logger->error($exception->getMessage());
        }

    }
}