<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Entity;

use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use App\Notification\Management\Domain\Event\NotificationDiscardedEvent;
use App\Notification\Management\Domain\ValueObject\NotificationState;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Aggregate\AggregateRoot;
use DateTimeImmutable;

class Notification extends AggregateRoot
{
    private NotificationId $id;

    private NotificationType $type;

    private array $data;

    private Recipient $recipient;

    private NotificationState $state;

    private DateTimeImmutable $createdAt;

    public function __construct(NotificationId $id)
    {
        $this->id = $id;
    }

    public function getId(): NotificationId
    {
        return $this->id;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function setType(NotificationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

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

    public function getState(): NotificationState
    {
        return $this->state;
    }

    public function setState(NotificationState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public static function create(
        NotificationId $notificationId,
        NotificationType $notificationType,
        array $notificationData,
        Recipient $recipient,
    ): self
    {
        $notification = new self($notificationId);
        $notification->setType($notificationType);
        $notification->setData($notificationData);
        $notification->setRecipient($recipient);
        $notification->setState(NotificationState::SCHEDULED);
        $notification->setCreatedAt(new DateTimeImmutable());

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
        $notification = new self($notificationId);
        $notification->setType($notificationType);
        $notification->setData($notificationData);
        $notification->setRecipient($recipient);
        $notification->setState(NotificationState::DISCARDED);
        $notification->setCreatedAt(new DateTimeImmutable());

        $notification->recordDomainEvent(new NotificationDiscardedEvent($notificationId, $notificationType, $recipient, $notificationData));
        return $notification;
    }
}
