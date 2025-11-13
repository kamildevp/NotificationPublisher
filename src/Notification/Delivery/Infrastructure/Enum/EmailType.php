<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Enum;

use App\Notification\Shared\Domain\ValueObject\NotificationType;

enum EmailType
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
            self::INFO => 'email/info.html.twig',
            self::ALERT => 'email/alert.html.twig'
        };
    }

    public function getSubject(): string
    {
        return match($this){
            self::INFO => 'Support Info',
            self::ALERT => 'Important Alert'
        };
    }
}