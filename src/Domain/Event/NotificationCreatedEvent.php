<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationType;
use App\Domain\ValueObject\Recipient;

class NotificationCreatedEvent extends DomainEvent
{
    public function __construct(
        private Recipient $recipient,
        private string $message,
        private Channel $channel,
        private string $notificationId,
        private NotificationType $notificationType,
    )
    {
        
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function getNotificationId(): string
    {
        return $this->notificationId;
    }

    public function getNotificationType(): NotificationType
    {
        return $this->notificationType;
    }
} 