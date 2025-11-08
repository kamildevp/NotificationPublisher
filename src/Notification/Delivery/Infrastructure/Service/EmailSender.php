<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Service;

use App\Notification\Delivery\Infrastructure\Enum\EmailType;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailSender implements CommunicationChannelSenderInterface
{
    public function __construct(private MailerInterface $mailer)
    {
        
    }

    public function supports(CommunicationChannel $communicationChannel): bool
    {
        return $communicationChannel == CommunicationChannel::EMAIL;
    }

    public function send(Recipient $recipient, NotificationType $notificationType, array $notificationData): void
    {
        $emailType = EmailType::fromNotificationType($notificationType);
        $email = (new TemplatedEmail())->to($recipient->getEmail()->getValue())
            ->subject($emailType->getSubject())
            ->htmlTemplate($emailType->getTemplatePath())
            ->context($notificationData);

        $this->mailer->send($email);
    }
}