<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Enum;

use App\Notification\Shared\Domain\ValueObject\NotificationType;

enum SmsType
{
    case INFO;
    case ALERT;

    public static function fromNotificationType(NotificationType $notificationType): self
    {
        return match($notificationType){
            NotificationType::INFO => self::INFO,
            NotificationType::ALERT => self::ALERT
        };
    }

    public function getTemplatePath(): string
    {
        return match($this){
            self::INFO => 'sms/info.txt.twig',
            self::ALERT => 'sms/alert.txt.twig'
        };
    }
}