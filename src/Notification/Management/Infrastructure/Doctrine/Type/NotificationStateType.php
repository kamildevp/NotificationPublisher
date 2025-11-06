<?php

declare(strict_types=1);

namespace App\Notification\Management\Infrastructure\Doctrine\Type;

use App\Notification\Management\Domain\ValueObject\NotificationState;
use App\Shared\Infrastructure\Doctrine\Type\BackedStringEnumType;

final class NotificationStateType extends BackedStringEnumType
{
    public function getName(): string
    {
        return 'notification_state';
    }

    protected function typeClassName(): string
    {
        return NotificationState::class;
    }
}
