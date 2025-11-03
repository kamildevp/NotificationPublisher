<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Service;

use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use DateTimeImmutable;

class RatePolicy implements NotificationPolicyInterface
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private array $config,
    )
    {
        
    }

    public function canNotificationBeSent(Recipient $recipient, NotificationType $notificationType, array $notificationData): bool
    {
        $rateLimit = $this->config[$notificationType->value]['max_per_hour'] ?? null;
        $to = new DateTimeImmutable();
        $from = $to->modify('-1 hour');
        $notificationCount = $this->notificationRepository->getRecipientNotificationCount($recipient, $notificationType, $from, $to);

        return $notificationCount < $rateLimit;
    }
}