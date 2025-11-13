<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Application\Service;

use App\Notification\Delivery\Application\Command\DeliverNotificationCommand;
use App\Notification\Delivery\Application\Service\DeliverNotificationHandler;
use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Event\DeliveryCompletedEvent;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Delivery\Infrastructure\Service\CommunicationChannelSenderInterface;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Event\DomainEventInterface;
use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeliverNotificationHandlerTest extends TestCase
{
    private CommunicationChannelSenderInterface&MockObject $senderMock;
    private DeliveryRepositoryInterface&MockObject $deliveryRepositoryMock;
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private DeliverNotificationHandler $service;

    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->senderMock = $this->createMock(CommunicationChannelSenderInterface::class);
        $this->deliveryRepositoryMock = $this->createMock(DeliveryRepositoryInterface::class);
        $this->service = new DeliverNotificationHandler(
            new ArrayIterator([$this->senderMock]),
            $this->deliveryRepositoryMock,
            $this->eventDispatcherMock
        );
    }
    
    public function testInvokeSendsNotificationUsingMatchingSender(): void
    {
        $command = new DeliverNotificationCommand(
            new DeliveryId('242f9242-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            CommunicationChannel::EMAIL,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $this->senderMock
            ->expects($this->once())
            ->method('supports')
            ->with($command->getCommunicationChannel())
            ->willReturn(true);

        $this->senderMock
            ->expects($this->once())
            ->method('send')
            ->with(
                $command->getNotificationRecipient(),
                $command->getNotificationType(),
                $command->getNotificationData()
            );

        $eventMock = $this->createMock(DomainEventInterface::class);
        $deliveryMock = $this->createMock(Delivery::class);
        $deliveryMock
            ->expects($this->once())
            ->method('markCompleted');

        $deliveryMock
            ->method('pullDomainEvents')
            ->willReturn([$eventMock]);

        $this->deliveryRepositoryMock
            ->method('findById')
            ->with($command->getDeliveryId())
            ->willReturn($deliveryMock);

        $this->deliveryRepositoryMock
            ->method('save')
            ->with($deliveryMock);

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($eventMock);

        ($this->service)($command);
    }

    public function testInvokeReturnsWhenNoMatchingSender(): void
    {
        $command = new DeliverNotificationCommand(
            new DeliveryId('242f9242-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            CommunicationChannel::EMAIL,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $this->senderMock
            ->expects($this->once())
            ->method('supports')
            ->with($command->getCommunicationChannel())
            ->willReturn(false);

        $this->senderMock
            ->expects($this->never())
            ->method('send');

        ($this->service)($command);
    }
}