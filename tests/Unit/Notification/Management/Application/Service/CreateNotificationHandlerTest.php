<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Management\Application\Service;

use App\Notification\Management\Application\Command\CreateNotificationCommand;
use App\Notification\Management\Application\DTO\RecipientDTO;
use App\Notification\Management\Application\Service\CreateNotificationHandler;
use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Management\Domain\Factory\NotificationFactory;
use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Event\DomainEventInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateNotificationHandlerTest extends TestCase
{
    private NotificationRepositoryInterface&MockObject $notificationRepositoryMock;
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private NotificationFactory&MockObject $notificationFactoryMock;
    private CreateNotificationHandler $service;

    protected function setUp(): void
    {
        $this->notificationRepositoryMock = $this->createMock(NotificationRepositoryInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $this->service = new CreateNotificationHandler(
            $this->notificationRepositoryMock, 
            $this->eventDispatcherMock,
            $this->notificationFactoryMock,
        );
    }

    public function testInvokeCreatesNotificationAndDispatchesDomainEvents(): void
    {
        $recipientDto = new RecipientDTO('2a8045fd', 'user@example.com', '+48213721372');
        $command = new CreateNotificationCommand(
            $recipientDto,
            NotificationType::ALERT->value,
            ['message' => 'test']
        );

        $eventMock = $this->createMock(DomainEventInterface::class);
        $notificationMock = $this->createMock(Notification::class);
        $notificationMock
            ->method('pullDomainEvents')
            ->willReturn([$eventMock]);

        $this->notificationFactoryMock
            ->method('createNotification')
            ->with(
                $this->callback(fn($arg) => $arg instanceof NotificationId),
                $this->callback(fn($arg) => 
                    $arg instanceof Recipient &&
                    $arg->getCustomerIdentifier() == $recipientDto->getCustomerIdentifier() &&
                    $arg->getEmail()->getValue() == $recipientDto->getEmail() &&
                    $arg->getPhone()->getValue() == $recipientDto->getPhone()
                ),
                NotificationType::ALERT,
                $command->getNotificationData()
            )
            ->willReturn($notificationMock);

        $this->notificationRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($notificationMock);

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($eventMock);

        ($this->service)($command);
    }
}