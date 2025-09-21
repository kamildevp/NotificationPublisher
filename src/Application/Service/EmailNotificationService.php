<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Enum\Channel;
use App\Domain\Event\NotificationCreatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailNotificationService implements NotificationChannelServiceInterface
{
    public function __construct(private MailerInterface $mailer)
    {
        
    }

    public function supports(Channel $channel): bool
    {
        return $channel == Channel::EMAIL;
    }

    public function send(NotificationCreatedEvent $event): void
    {
        $emailType = $event->getNotificationType()->getEmailType();
        $email = (new TemplatedEmail())->to($event->getRecipient()->getEmail())
            ->subject($emailType->getSubject())
            ->htmlTemplate($emailType->getTemplatePath())
            ->context(['message' => $event->getMessage()]);

        $this->mailer->send($email);
    }
}