<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Event;

use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Event\DomainEventInterface;

final class NotificationDiscardedEvent implements DomainEventInterface
{
    public function __construct(
        private NotificationId $notificationId,
        private NotificationType $notificationType,
        private Recipient $notificationRecipient,
        private array $notificationData,
    )
    {
        
    }

    public function getNotificationId(): NotificationId
    {
        return $this->notificationId;
    }

    public function getNotificationType(): NotificationType
    {
        return $this->notificationType;
    }

    public function getNotificationRecipient(): Recipient
    {
        return $this->notificationRecipient;
    }

    public function getNotificationData(): array
    {
        return $this->notificationData;
    }
}