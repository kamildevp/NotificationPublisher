<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Repository;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Shared\Domain\ValueObject\PaginationResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Delivery>
 */
class DeliveryRepository extends ServiceEntityRepository implements DeliveryRepositoryInterface
{
    const MAX_ENTRIES_PER_PAGE = 100;

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

    public function paginate(int $page, int $perPage, ?string $recipientCustomerIdentifier = null): PaginationResult
    {
        $qb = $this->createQueryBuilder('d');
        if($recipientCustomerIdentifier){
            $qb->where('d.recipient.customerIdentifier = :recipientCustomerIdentifier')
            ->setParameter('recipientCustomerIdentifier', $recipientCustomerIdentifier);
        }
        $qb->orderBy('d.scheduledAt', 'DESC');

        $offset = ($page - 1) * $perPage;
        $perPage = min($perPage, self::MAX_ENTRIES_PER_PAGE);    
        $qb->setFirstResult($offset)->setMaxResults($perPage);

        $paginator = new Paginator($qb);
        $items = iterator_to_array($paginator);
        $total = count($paginator);

        return new PaginationResult(
            $items,
            $page,
            $perPage,
            (int)ceil($total / $perPage),
            $total
        );
    }
}
