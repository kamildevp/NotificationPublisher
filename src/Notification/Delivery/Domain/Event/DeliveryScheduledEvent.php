<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Event;

use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Event\DomainEventInterface;

final class DeliveryScheduledEvent implements DomainEventInterface
{
    public function __construct(
        private DeliveryId $deliveryId,
        private NotificationId $notificationId,
        private NotificationType $notificationType,
        private CommunicationChannel $communicationChannel,
        private Recipient $notificationRecipient,
        private array $notificationData,
    )
    {
        
    }

    public function getDeliveryId(): DeliveryId
    {
        return $this->deliveryId;
    }

    public function getNotificationId(): NotificationId
    {
        return $this->notificationId;
    }

    public function getNotificationType(): NotificationType
    {
        return $this->notificationType;
    }

    public function getCommunicationChannel(): CommunicationChannel
    {
        return $this->communicationChannel;
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