<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Config;

class AvailableChannelsConfig
{
    /** @param string[] $channels */
    public function __construct(public readonly array $channels)
    {
        
    }
}