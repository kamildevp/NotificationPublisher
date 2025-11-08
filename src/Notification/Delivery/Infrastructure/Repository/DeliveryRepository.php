<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Repository;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Delivery>
 */
class DeliveryRepository extends ServiceEntityRepository implements DeliveryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    public function save(Delivery $delivery): void
    {
        $this->getEntityManager()->persist($delivery);
        $this->getEntityManager()->flush();
    }

    public function findById(DeliveryId $id): ?Delivery
    {
        return parent::find($id);
    }
}
