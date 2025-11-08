<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Repository;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;

interface DeliveryRepositoryInterface
{
    public function save(Delivery $delivery): void;

    public function findById(DeliveryId $id): ?Delivery;
}