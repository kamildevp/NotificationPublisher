<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Repository;

use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use DateTimeInterface;

interface NotificationRepositoryInterface
{
    public function save(Notification $notification): void;

    public function getRecipientNotificationCount(
        Recipient $recipient, 
        NotificationType $notificationType, 
        DateTimeInterface $from, 
        DateTimeInterface $to
    ): int;
}