<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Repository;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Shared\Domain\ValueObject\PaginationResult;

interface DeliveryRepositoryInterface
{
    public function save(Delivery $delivery): void;

    public function findById(DeliveryId $id): ?Delivery;

    public function paginate(int $page, int $perPage, ?string $recipientCustomerIdentifier = null): PaginationResult;
}