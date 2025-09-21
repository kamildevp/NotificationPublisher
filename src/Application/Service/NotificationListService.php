<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Common\NotificationListDTO;
use App\Application\Common\PaginationResult;
use App\Infrastructure\Repository\NotificationRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NotificationListService
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private NormalizerInterface $normalizer
    ) {

    }

    public function listNotifications(NotificationListDTO $notificationListDTO): PaginationResult
    {
        $page = $notificationListDTO->getPage();
        $perPage = $notificationListDTO->getPerPage();
        $recipientIdentifier = $notificationListDTO->getRecipientIdentifier();
        $paginator = $this->notificationRepository->paginate(
            $page,
            $perPage,
            $recipientIdentifier
        );

        $items = iterator_to_array($paginator);
        $normalizedItems = $this->normalizer->normalize($items);
        $total = count($paginator);


        return new PaginationResult(
            $normalizedItems,
            $notificationListDTO->getPage(),
            $perPage,
            (int)ceil($total / $perPage),
            $total
        );
    }
}
