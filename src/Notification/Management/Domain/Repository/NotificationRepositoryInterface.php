<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Repository;

use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\ValueObject\PaginationResult;
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

    public function paginate(int $page, int $perPage, ?string $recipientCustomerIdentifier = null): PaginationResult;
}