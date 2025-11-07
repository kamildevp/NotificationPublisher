<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Service;

class DeliveryRetryPolicy
{
    public function __construct(
        private int $maxRetries,
        private int $retryDelay,
        private int $retryDelayMultiplier,
    )
    {

    }

    public function shouldRetry(int $attemptNr): bool
    {
        return $attemptNr < $this->maxRetries;
    }

    public function getRetryDelay(int $attemptNr): int
    {
        return $attemptNr*$this->retryDelayMultiplier*$this->retryDelay;
    }
}