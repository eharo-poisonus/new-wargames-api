<?php

namespace App\Communication\Emails\Infrastructure;

use App\Communication\Emails\Domain\EmailMessage;
use App\Communication\Emails\Domain\EmailSender;
use App\Communication\Emails\Domain\Exceptions\EmailDeliveryFailedException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final readonly class SymfonyMailerEmailSender implements EmailSender
{
    public function __construct(
        private MailerInterface $mailer,
        private string $mailerFromAddress,
        private string $mailerFromName
    ) {
    }

    public function send(EmailMessage $emailMessage): void
    {
        $email = new Email()
            ->from(new Address($this->mailerFromAddress, $this->mailerFromName))
            ->to(new Address($emailMessage->to(), $emailMessage->toName()))
            ->subject($emailMessage->subject())
            ->text($emailMessage->textBody())
            ->html($emailMessage->htmlBody());

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface) {
            throw new EmailDeliveryFailedException();
        }
    }
}
