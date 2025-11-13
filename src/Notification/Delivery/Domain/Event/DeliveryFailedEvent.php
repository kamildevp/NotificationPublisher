<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Event;

use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Shared\Domain\Event\DomainEventInterface;

final class DeliveryFailedEvent implements DomainEventInterface
{
    public function __construct(
        private DeliveryId $deliveryId,
    )
    {
        
    }

    public function getDeliveryId(): DeliveryId
    {
        return $this->deliveryId;
    }
}