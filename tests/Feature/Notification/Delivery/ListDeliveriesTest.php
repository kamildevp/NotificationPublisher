<?php

declare(strict_types=1);

namespace App\Tests\Feature\Notification\Delivery;

use App\DataFixtures\Notification\Delivery\ListDeliveriesFixtures;
use App\Notification\Delivery\Infrastructure\Repository\DeliveryRepository;
use App\Tests\Feature\Notification\Delivery\DataProvider\ListDeliveriesDataProvider;
use App\Tests\Utils\Attribute\Fixtures;
use App\Tests\Utils\BaseWebTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class ListDeliveriesTest extends BaseWebTestCase
{
    protected InMemoryTransport $messengerTransport;
    protected DeliveryRepository $deliveryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deliveryRepository = $this->container->get(DeliveryRepository::class);
    }

    #[Fixtures([ListDeliveriesFixtures::class])]
    #[DataProviderExternal(ListDeliveriesDataProvider::class, 'listDataCases')]
    public function testList(int $page, int $perPage, int $total, ?string $recipientCustomerIdentifier = null): void
    {
        $path = '/api/delivery?' . http_build_query([
            'page' => $page,
            'per_page' => $perPage,
            ...array_filter(['recipient_customer_identifier' => $recipientCustomerIdentifier])
        ]);
        $responseData = $this->getSuccessfulResponseData($this->client, 'GET', $path);

        $offset = ($page - 1) * $perPage;
        $items = $this->deliveryRepository->findBy(
            array_filter(['recipient.customerIdentifier' => $recipientCustomerIdentifier]), 
            [], 
            $perPage, 
            $offset
        );
        $formattedItems = $this->normalize($items, []);

        $this->assertPaginatorResponse($responseData, $page, $perPage, $total, $formattedItems);
    }
}
