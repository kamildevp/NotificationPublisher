<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Enum;

use App\Notification\Management\UI\DTO\AlertNotificationDataDTO;
use App\Notification\Management\UI\DTO\InfoNotificationDataDTO;
use App\Notification\Management\UI\Enum\Trait\ValuesTrait;

enum NotificationType: string
{
    use ValuesTrait;

    case INFO = 'info';
    case ALERT = 'alert';

    public function getDataDTOClass(): string
    {
        return match($this){
            self::INFO => InfoNotificationDataDTO::class,
            self::ALERT => AlertNotificationDataDTO::class,
        };
    }

    public function getDataExample(): array
    {
        return match($this){
            self::INFO => [
                'message' => 'My message',
            ],
            self::ALERT => [
                'message' => 'My message',
            ],
        };
    }
}