<?php

declare(strict_types=1);

namespace App\Notification\Shared\Infrastructure\Doctrine\Type;

use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Shared\Infrastructure\Doctrine\Type\AggregateRootIdType;

final class NotificationIdType extends AggregateRootIdType
{
    public function getName(): string
    {
        return 'notification_id';
    }

    protected function typeClassName(): string
    {
        return NotificationId::class;
    }
}
