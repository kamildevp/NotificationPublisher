<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\DataFixtures\NotificationListFixtures;
use App\Infrastructure\Repository\NotificationRepository;
use App\Tests\E2E\DataProvider\NotificationListDataProvider;
use App\Tests\Utils\Attribute\Fixtures;
use App\Tests\Utils\BaseWebTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class ListNotificationTest extends BaseWebTestCase
{
    protected InMemoryTransport $messengerTransport;
    protected NotificationRepository $notificationRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationRepository = $this->container->get(NotificationRepository::class);
    }

    #[Fixtures([NotificationListFixtures::class])]
    #[DataProviderExternal(NotificationListDataProvider::class, 'listDataCases')]
    public function testList(int $page, int $perPage, int $total, ?string $recipientIdentifier = null): void
    {
        $path = '/api/notification?' . http_build_query([
            'page' => $page,
            'per_page' => $perPage,
            ...array_filter(['recipient_identifier' => $recipientIdentifier])
        ]);
        $responseData = $this->getSuccessfulResponseData($this->client, 'GET', $path);

        $offset = ($page - 1) * $perPage;
        $items = $this->notificationRepository->findBy(array_filter(['recipientIdentifier' => $recipientIdentifier]), ['id' => 'ASC'], $perPage, $offset);
        $formattedItems = $this->normalize($items, []);

        $this->assertPaginatorResponse($responseData, $page, $perPage, $total, $formattedItems);
    }
}
