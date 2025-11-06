<?php

namespace App\Notification\Management\Infrastructure\Repository;

use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use App\Notification\Management\Domain\ValueObject\NotificationState;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository implements NotificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $notification): void
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();
    }

    public function getRecipientNotificationCount(
        Recipient $recipient, 
        NotificationType $notificationType, 
        DateTimeInterface $from, 
        DateTimeInterface $to
    ): int
    {
        $qb = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.recipient.customerIdentifier = :recipientIdentifier')
            ->andWhere('n.state != :discardedState')
            ->andWhere('n.type = :type')
            ->andWhere('n.createdAt >= :from')
            ->andWhere('n.createdAt <= :to')
            ->setParameter('recipientIdentifier', $recipient->getCustomerIdentifier())
            ->setParameter('discardedState', NotificationState::DISCARDED->value)
            ->setParameter('type', $notificationType->value)
            ->setParameter('from', $from)
            ->setParameter('to', $to);
        
        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
