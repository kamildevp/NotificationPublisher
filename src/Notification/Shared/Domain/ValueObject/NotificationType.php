<?php

declare(strict_types=1);

namespace App\Notification\Shared\Domain\ValueObject;

enum NotificationType: string
{
    case INFO = 'info';
    case ALERT = 'alert';
}