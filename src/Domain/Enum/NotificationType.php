<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use App\Domain\Enum\Trait\ValuesTrait;

enum NotificationType: string
{
    use ValuesTrait;

    case INFO = 'info';
    case ALERT = 'alert';

    public function getEmailType(): EmailType
    {
        return match($this){
            self::INFO => EmailType::INFO,
            self::ALERT => EmailType::ALERT
        };
    }
}