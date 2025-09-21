<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Messenger;

use App\Infrastructure\Messenger\NotificationCreatedHandler;
use App\Application\Service\NotificationChannelServiceInterface;
use App\Application\Service\NotificationChannelServiceResolver;
use App\Infrastructure\Repository\NotificationRepository;
use App\Domain\Entity\Notification;
use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Event\NotificationCreatedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class NotificationCreatedHandlerTest extends TestCase
{
    private NotificationChannelServiceResolver&MockObject $resolverMock;
    private NotificationRepository&MockObject $notificationRepositoryMock;
    private NotificationCreatedHandler $handler;
    private NotificationChannelServiceInterface&MockObject $channelServiceMock;

    protected function setUp(): void
    {
        $this->resolverMock = $this->createMock(NotificationChannelServiceResolver::class);
        $this->notificationRepositoryMock = $this->createMock(NotificationRepository::class);
        $this->channelServiceMock = $this->createMock(NotificationChannelServiceInterface::class);

        $this->handler = new NotificationCreatedHandler(
            $this->resolverMock,
            $this->notificationRepositoryMock
        );
    }

    public function testInvokeSendsNotificationAndUpdatesStatus(): void
    {
        $notificationMock = $this->createMock(Notification::class);
        $eventMock = $this->createMock(NotificationCreatedEvent::class);

        $eventMock->method('getChannel')->willReturn(Channel::EMAIL);
        $eventMock->method('getNotificationId')->willReturn('notif-123');

        $this->resolverMock
            ->expects($this->once())
            ->method('resolve')
            ->with(Channel::EMAIL)
            ->willReturn($this->channelServiceMock);

        $this->channelServiceMock
            ->expects($this->once())
            ->method('send')
            ->with($eventMock);

        $this->notificationRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with('notif-123')
            ->willReturn($notificationMock);

        $notificationMock
            ->expects($this->once())
            ->method('setStatus')
            ->with(NotificationStatus::SENT);

        $notificationMock
            ->expects($this->once())
            ->method('setSentAt')
            ->with($this->isInstanceOf(DateTimeImmutable::class));

        $this->notificationRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($notificationMock);

        ($this->handler)($eventMock);
    }

    public function testInvokeDoesNothingIfNotificationNotFound(): void
    {
        $eventMock = $this->createMock(NotificationCreatedEvent::class);
        $eventMock->method('getChannel')->willReturn(Channel::EMAIL);
        $eventMock->method('getNotificationId')->willReturn('notif-123');

        $this->resolverMock
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($this->channelServiceMock);

        $this->channelServiceMock
            ->expects($this->once())
            ->method('send')
            ->with($eventMock);

        $this->notificationRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with('notif-123')
            ->willReturn(null);

        $this->notificationRepositoryMock
            ->expects($this->never())
            ->method('save');

        ($this->handler)($eventMock);
    }
}
