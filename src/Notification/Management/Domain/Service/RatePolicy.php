<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Service;

use App\Notification\Management\Domain\Config\RateConfig;
use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use DateTimeImmutable;
use InvalidArgumentException;

class RatePolicy implements NotificationManagementPolicyInterface
{
    /** @param array<string,RateConfig> $config*/
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private array $config,
    )
    {

    }

    public function canNotificationBeSent(Recipient $recipient, NotificationType $notificationType, array $notificationData): bool
    {
        $rateConfig =  $this->config[$notificationType->value] ?? null;
        if(!$rateConfig){
            return true;
        }

        if(!$rateConfig instanceof RateConfig)
        {
            throw new InvalidArgumentException(sprintf('Expected %s for notification type %s config, got %s', RateConfig::class, $notificationType->value, get_debug_type($rateConfig)));
        }

        $rateLimit = $rateConfig->maxPerHour;
        $to = new DateTimeImmutable();
        $from = $to->modify('-1 hour');
        $notificationCount = $this->notificationRepository->getRecipientNotificationCount($recipient, $notificationType, $from, $to);

        return $notificationCount < $rateLimit;
    }
}