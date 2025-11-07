<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Entity;

use App\Notification\Delivery\Domain\Event\DeliveryScheduledEvent;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Aggregate\AggregateRoot;

class Delivery extends AggregateRoot
{
    private function __construct(
        private DeliveryId $id,
        private NotificationId $notificationId,
        private NotificationType $notificationType,
        private CommunicationChannel $communicationChannel,
        private array $notificationData,
        private Recipient $recipient,
    )
    {

    }

    public function getId(): DeliveryId
    {
        return $this->id;
    }

    public function getNotificationId(): NotificationId
    {
        return $this->notificationId;
    }

    public function setNotificationId(NotificationId $notificationId): self
    {
        $this->notificationId = $notificationId;

        return $this;
    }

    public function getNotificationType(): NotificationType
    {
        return $this->notificationType;
    }

    public function setNotificationType(NotificationType $notificationType): self
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    public function getCommunicationChannel(): CommunicationChannel
    {
        return $this->communicationChannel;
    }

    public function setCommunicationChannel(CommunicationChannel $communicationChannel): self
    {
        $this->communicationChannel = $communicationChannel;

        return $this;
    }

    public function getNotificationData(): array
    {
        return $this->notificationData;
    }

    public function setNotificationData(array $notificationData): self
    {
        $this->notificationData = $notificationData;

        return $this;
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    public function setRecipient(Recipient $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public static function schedule(
        DeliveryId $deliveryId,
        NotificationId $notificationId,
        NotificationType $notificationType,
        CommunicationChannel $communicationChannel,
        array $notificationData,
        Recipient $recipient,
    ): self
    {
        $delivery = new self(
            $deliveryId,
            $notificationId,
            $notificationType,
            $communicationChannel,
            $notificationData,
            $recipient,
        );

        $delivery->recordDomainEvent(new DeliveryScheduledEvent($deliveryId, $notificationId, $notificationType, $communicationChannel, $recipient, $notificationData));
        return $delivery;
    }
}
