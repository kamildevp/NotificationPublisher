<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Config;

class RateConfig 
{
    public function __construct(public readonly int $maxPerHour)
    {
        
    }
}