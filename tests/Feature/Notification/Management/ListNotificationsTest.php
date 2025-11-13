<?php

declare(strict_types=1);

namespace App\Tests\Feature\Notification\Management;

use App\DataFixtures\Notification\Management\ListNotificationsFixtures;
use App\Notification\Management\Infrastructure\Repository\NotificationRepository;
use App\Tests\Feature\Notification\Management\DataProvider\ListNotificationsDataProvider;
use App\Tests\Utils\Attribute\Fixtures;
use App\Tests\Utils\BaseWebTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class ListNotificationsTest extends BaseWebTestCase
{
    protected InMemoryTransport $messengerTransport;
    protected NotificationRepository $notificationRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationRepository = $this->container->get(NotificationRepository::class);
    }

    #[Fixtures([ListNotificationsFixtures::class])]
    #[DataProviderExternal(ListNotificationsDataProvider::class, 'listDataCases')]
    public function testList(int $page, int $perPage, int $total, ?string $recipientCustomerIdentifier = null): void
    {
        $path = '/api/notification?' . http_build_query([
            'page' => $page,
            'per_page' => $perPage,
            ...array_filter(['recipient_customer_identifier' => $recipientCustomerIdentifier])
        ]);
        $responseData = $this->getSuccessfulResponseData($this->client, 'GET', $path);

        $offset = ($page - 1) * $perPage;
        $items = $this->notificationRepository->findBy(
            array_filter(['recipient.customerIdentifier' => $recipientCustomerIdentifier]), 
            [], 
            $perPage, 
            $offset
        );
        $formattedItems = $this->normalize($items, []);

        $this->assertPaginatorResponse($responseData, $page, $perPage, $total, $formattedItems);
    }
}
