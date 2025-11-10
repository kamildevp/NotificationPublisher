<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Application\Command;

use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;

class DeliverNotificationCommand
{
    public function __construct(
        private DeliveryId $deliveryId,
        private NotificationType $notificationType,
        private CommunicationChannel $communicationChannel,
        private Recipient $notificationRecipient,
        private array $notificationData,
        private int $attemptNr = 1,
    )
    {
        
    }

    public function getDeliveryId(): DeliveryId
    {
        return $this->deliveryId;
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

    public function getAttemptNr(): int
    {
        return $this->attemptNr;
    }
}