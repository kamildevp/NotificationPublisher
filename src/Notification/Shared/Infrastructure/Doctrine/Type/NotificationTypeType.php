<?php

declare(strict_types=1);

namespace App\Notification\Shared\Infrastructure\Doctrine\Type;

use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Shared\Infrastructure\Doctrine\Type\BackedStringEnumType;

final class NotificationTypeType extends BackedStringEnumType
{
    public function getName(): string
    {
        return 'notification_type';
    }

    protected function typeClassName(): string
    {
        return NotificationType::class;
    }
}
