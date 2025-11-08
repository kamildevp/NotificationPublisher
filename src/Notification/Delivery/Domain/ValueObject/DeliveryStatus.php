<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\ValueObject;

enum DeliveryStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}