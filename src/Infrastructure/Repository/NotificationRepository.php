<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Notification;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    const RECENT_PERIOD = '-1 hour';
    const MAX_ENTRIES_PER_PAGE = 100;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $notification): void
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();
    }

    public function getRecentNotificationCountForRecipient(string $recipientIdentifier, string $type): int
    {
        $sentFrom = new DateTime(self::RECENT_PERIOD);
        $qb = $this->createQueryBuilder('n')
                    ->select('COUNT(n.id)')
                    ->where('n.recipientIdentifier = :recipientIdentifier')
                    ->andWhere('n.type = :type')
                    ->andWhere('n.sentAt IS NOT NULL')
                    ->andWhere('n.sentAt > :sentAt')
                    ->setParameter('recipientIdentifier', $recipientIdentifier)
                    ->setParameter('type', $type)
                    ->setParameter('sentAt', $sentFrom);
        
        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function paginate(int $page, int $perPage, ?string $recipientIdentifier = null): Paginator
    {
        $qb = $this->createQueryBuilder('n');
        if($recipientIdentifier){
            $qb->where('n.recipientIdentifier = :recipientIdentifier')->setParameter('recipientIdentifier', $recipientIdentifier);
        }
        $qb->orderBy('n.id', 'ASC');

        $offset = ($page - 1) * $perPage;
        $perPage = min($perPage, self::MAX_ENTRIES_PER_PAGE);    
        $qb->setFirstResult($offset)->setMaxResults($perPage);

        return new Paginator($qb);
    }
}
