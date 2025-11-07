<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Repository;

use App\Notification\Delivery\Domain\Entity\Delivery;

interface DeliveryRepositoryInterface
{
    public function save(Delivery $delivery): void;
}