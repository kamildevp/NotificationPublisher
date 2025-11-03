<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Service;

use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;

interface NotificationPolicyInterface
{
    public function canNotificationBeSent(Recipient $recipient, NotificationType $notificationType, array $notificationData): bool;
}