<?php

declare(strict_types=1);

namespace App\Domain\Policy;

use App\Domain\Enum\NotificationType;

class RatePolicy
{
    private array $rateLimits;

    public function __construct(private int $alertRateLimit)
    {
        $this->rateLimits = [
            NotificationType::ALERT->value => $alertRateLimit
        ];
    }

    public function isRateLimitExceeded(NotificationType $notificationType, int $recentNotificationCount): bool
    {
        $rateLimit = array_key_exists($notificationType->value, $this->rateLimits) ? $this->rateLimits[$notificationType->value] : null;
        return !is_null($rateLimit) ? $rateLimit <= $recentNotificationCount : false;
    }
}