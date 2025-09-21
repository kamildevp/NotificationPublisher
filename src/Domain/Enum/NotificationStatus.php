<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use App\Domain\Enum\Trait\ValuesTrait;

enum NotificationStatus: string
{
    use ValuesTrait;

    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}