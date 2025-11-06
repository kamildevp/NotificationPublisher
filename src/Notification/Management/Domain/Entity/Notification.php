<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Entity;

use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use App\Notification\Management\Domain\Event\NotificationDiscardedEvent;
use App\Notification\Management\Domain\ValueObject\NotificationState;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;

class Notification extends AggregateRoot
{
    private function __construct(
        private NotificationId $id,
        private NotificationType $type,
        private array $data,
        private Recipient $recipient,
        private NotificationState $state,
        private DateTimeImmutable $createdAt,
    )
    {

    }

    public function getId(): NotificationId
    {
        return $this->id;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    public function getState(): NotificationState
    {
        return $this->state;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public static function create(
        NotificationId $notificationId,
        NotificationType $notificationType,
        array $notificationData,
        Recipient $recipient,
    ): self
    {
        $notification = new self(
            $notificationId,
            $notificationType,
            $notificationData,
            $recipient,
            NotificationState::SCHEDULED,
            new DateTimeImmutable()
        );

        $notification->recordDomainEvent(new NotificationCreatedEvent($notificationId, $notificationType, $recipient, $notificationData));
        return $notification;
    }

    public static function discard(
        NotificationId $notificationId,
        NotificationType $notificationType,
        array $notificationData,
        Recipient $recipient,
    ): self
    {
        $notification = new self(
            $notificationId,
            $notificationType,
            $notificationData,
            $recipient,
            NotificationState::DISCARDED,
            new DateTimeImmutable()
        );

        $notification->recordDomainEvent(new NotificationDiscardedEvent($notificationId, $notificationType, $recipient, $notificationData));
        return $notification;
    }
}
