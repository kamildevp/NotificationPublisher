<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Enum\Channel;
use App\Domain\Event\NotificationCreatedEvent;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

class SmsNotificationService implements NotificationChannelServiceInterface
{
    public function __construct(private TexterInterface $texter)
    {
        
    }

    public function supports(Channel $channel): bool
    {
        return $channel == Channel::SMS;
    }

    public function send(NotificationCreatedEvent $event): void
    {
        $smsMessage = new SmsMessage($event->getRecipient()->getPhone(), $event->getMessage());
        
        $this->texter->send($smsMessage);
    }
}