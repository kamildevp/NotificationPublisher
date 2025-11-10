<?php

declare(strict_types=1);

namespace App\Notification\Management\Application\Service;

use App\Notification\Management\Application\Query\ListNotificationsQuery;
use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsMessageHandler]
class ListNotificationsHandler
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private NormalizerInterface $normalizer,
    )
    {

    }

    /** @return mixed[] */
    public function __invoke(ListNotificationsQuery $query): array
    {
        $paginationResult = $this->notificationRepository->paginate(
            $query->getPage(), 
            $query->getPerPage(), 
            $query->getRecipientCustomerIdentifier()
        );

        return $this->normalizer->normalize($paginationResult);
    }
}