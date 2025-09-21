<?php

declare(strict_types=1);

namespace Tests\Application\Command;

use App\Application\Command\SendNotificationCommand;
use App\Application\Command\SendNotificationCommandHandler;
use App\Application\Common\RecipientDTO;
use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationType;
use App\Domain\Event\DomainEvent;
use App\Domain\Factory\NotificationFactory;
use App\Domain\ValueObject\Recipient;
use App\Domain\Entity\Notification;
use App\Infrastructure\Repository\NotificationRepository;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class SendNotificationCommandHandlerTest extends TestCase
{
    private NotificationFactory&MockObject $notificationFactoryMock;
    private NotificationRepository&MockObject $notificationRepositoryMock;
    private MessageBusInterface&MockObject $eventBusMock;
    private SendNotificationCommandHandler $handler;

    protected function setUp(): void
    {
        $this->notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $this->notificationRepositoryMock = $this->createMock(NotificationRepository::class);
        $this->eventBusMock = $this->createMock(MessageBusInterface::class);

        $this->handler = new SendNotificationCommandHandler(
            $this->notificationFactoryMock,
            $this->notificationRepositoryMock,
            $this->eventBusMock
        );
    }

    public function testInvokeSavesNotificationAndDispatchesEvents(): void
    {
        $recipientDto = new RecipientDTO('user123', 'user@example.com', '123456789');
        $command = new SendNotificationCommand(
            $recipientDto,
            NotificationType::INFO->value,
            'System info message',
            [Channel::EMAIL->value]
        );

        $eventMock1 = $this->createMock(DomainEvent::class);
        $eventMock2 = $this->createMock(DomainEvent::class);

        $notificationMock = $this->createMock(Notification::class);
        $notificationMock->method('popEvents')->willReturn([$eventMock1, $eventMock2]);

        $this->notificationFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(
                NotificationType::INFO,
                'System info message',
                $this->isInstanceOf(Recipient::class),
                Channel::EMAIL
            )
            ->willReturn($notificationMock);

        $this->notificationRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($notificationMock);

        $this->eventBusMock
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function ($event) {
                $this->assertInstanceOf(DomainEvent::class, $event);
                return new Envelope($event);
            });

        ($this->handler)($command);
    }

    public function testInvokeSkipsOnDomainException(): void
    {
        $recipientDto = new RecipientDTO('user123', 'user@example.com', '123456789');
        $command = new SendNotificationCommand(
            $recipientDto,
            NotificationType::INFO->value,
            'System info message',
            [Channel::EMAIL->value]
        );

        $this->notificationFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new DomainException());

        $this->notificationRepositoryMock
            ->expects($this->never())
            ->method('save');

        $this->eventBusMock
            ->expects($this->never())
            ->method('dispatch');

        ($this->handler)($command);
    }
}
