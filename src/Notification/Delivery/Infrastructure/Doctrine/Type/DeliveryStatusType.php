<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Doctrine\Type;

use App\Notification\Delivery\Domain\ValueObject\DeliveryStatus;
use App\Shared\Infrastructure\Doctrine\Type\BackedStringEnumType;

final class DeliveryStatusType extends BackedStringEnumType
{
    public function getName(): string
    {
        return 'delivery_status';
    }

    protected function typeClassName(): string
    {
        return DeliveryStatus::class;
    }
}
