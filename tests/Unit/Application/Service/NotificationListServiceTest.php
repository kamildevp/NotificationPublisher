<?php

declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Application\Common\NotificationListDTO;
use App\Application\Common\PaginationResult;
use App\Application\Service\NotificationListService;
use App\Infrastructure\Repository\NotificationRepository;
use ArrayIterator;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NotificationListServiceTest extends TestCase
{
    private NotificationRepository&MockObject $notificationRepositoryMock;
    private NormalizerInterface&MockObject $normalizerMock;
    private NotificationListService $service;

    protected function setUp(): void
    {
        $this->notificationRepositoryMock = $this->createMock(NotificationRepository::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->service = new NotificationListService(
            $this->notificationRepositoryMock,
            $this->normalizerMock
        );
    }

    public function testListNotificationsReturnsPaginationResult(): void
    {
        $dto = new NotificationListDTO(
            page: 1,
            perPage: 2,
            recipientIdentifier: 'user-123'
        );

        $notifications = [
            (object)['id' => 1, 'message' => 'Hello'],
            (object)['id' => 2, 'message' => 'World'],
            (object)['id' => 3, 'message' => 'Extra'],
        ];

        $paginatorMock = $this->createMock(Paginator::class);
        $paginatorMock->method('getIterator')->willReturn(new ArrayIterator($notifications));
        $paginatorMock->method('count')->willReturn(count($notifications));

        $this->notificationRepositoryMock
            ->expects(self::once())
            ->method('paginate')
            ->with(1, 2, 'user-123')
            ->willReturn($paginatorMock);

        $normalized = [
            ['id' => 1, 'message' => 'Hello'],
            ['id' => 2, 'message' => 'World'],
            ['id' => 3, 'message' => 'Extra'],
        ];

        $this->normalizerMock
            ->expects(self::once())
            ->method('normalize')
            ->with($notifications)
            ->willReturn($normalized);

        $result = $this->service->listNotifications($dto);

        self::assertInstanceOf(PaginationResult::class, $result);
        self::assertSame($normalized, $result->getItems());
        self::assertSame(1, $result->getPage());
        self::assertSame(2, $result->getPerPage());
        self::assertSame(2, $result->getPagesCount());
        self::assertSame(3, $result->getTotal());
    }
}
