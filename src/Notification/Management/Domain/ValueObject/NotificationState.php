<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\ValueObject;

enum NotificationState: string
{
    case SCHEDULED = 'scheduled';
    case DISCARDED = 'discarded';
}