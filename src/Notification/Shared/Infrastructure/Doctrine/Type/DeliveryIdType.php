<?php

declare(strict_types=1);

namespace App\Notification\Shared\Infrastructure\Doctrine\Type;

use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Shared\Infrastructure\Doctrine\Type\AggregateRootIdType;

final class DeliveryIdType extends AggregateRootIdType
{
    public function getName(): string
    {
        return 'delivery_id';
    }

    protected function typeClassName(): string
    {
        return DeliveryId::class;
    }
}
