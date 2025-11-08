<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Entity;

use App\Notification\Delivery\Domain\Event\DeliveryCompletedEvent;
use App\Notification\Delivery\Domain\Event\DeliveryFailedEvent;
use App\Notification\Delivery\Domain\Event\DeliveryScheduledEvent;
use App\Notification\Delivery\Domain\ValueObject\DeliveryStatus;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;

class Delivery extends AggregateRoot
{
    private function __construct(
        private DeliveryId $id,
        private NotificationId $notificationId,
        private NotificationType $notificationType,
        private CommunicationChannel $communicationChannel,
        private array $notificationData,
        private Recipient $recipient,
        private DeliveryStatus $status,
        private DateTimeImmutable $scheduledAt,
        private ?DateTimeImmutable $completedAt = null,
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

    public function getNotificationType(): NotificationType
    {
        return $this->notificationType;
    }

    public function getCommunicationChannel(): CommunicationChannel
    {
        return $this->communicationChannel;
    }

    public function getNotificationData(): array
    {
        return $this->notificationData;
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    public function getStatus(): DeliveryStatus
    {
        return $this->status;
    }

    public function getScheduledAt(): DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
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
            DeliveryStatus::PENDING,
            new DateTimeImmutable()
        );

        $delivery->recordDomainEvent(new DeliveryScheduledEvent($deliveryId, $notificationId, $notificationType, $communicationChannel, $recipient, $notificationData));
        return $delivery;
    }

    public function markCompleted(): self
    {
        $this->status = DeliveryStatus::COMPLETED;
        $this->completedAt = new DateTimeImmutable();
        $this->recordDomainEvent(new DeliveryCompletedEvent($this->getId()));
        return $this;
    }

    public function markFailed(): self
    {
        $this->status = DeliveryStatus::FAILED;
        $this->recordDomainEvent(new DeliveryFailedEvent($this->getId()));
        return $this;
    }
}
