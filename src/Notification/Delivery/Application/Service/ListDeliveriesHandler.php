<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Application\Service;

use App\Notification\Delivery\Application\Query\ListDeliveriesQuery;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsMessageHandler]
class ListDeliveriesHandler
{
    public function __construct(
        private DeliveryRepositoryInterface $deliveryRepository,
        private NormalizerInterface $normalizer,
    )
    {

    }

    /** @return mixed[] */
    public function __invoke(ListDeliveriesQuery $query): array
    {
        $paginationResult = $this->deliveryRepository->paginate(
            $query->getPage(), 
            $query->getPerPage(), 
            $query->getRecipientCustomerIdentifier()
        );

        return $this->normalizer->normalize($paginationResult);
    }
}