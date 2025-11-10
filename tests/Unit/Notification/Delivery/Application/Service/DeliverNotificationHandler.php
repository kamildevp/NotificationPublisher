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
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
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
            $this->createMock(DeliveryId::class),
            NotificationType::INFO,
            CommunicationChannel::EMAIL,
            $this->createMock(Recipient::class),
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

        $eventMock = $this->createMock(DeliveryCompletedEvent::class);
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
            $this->createMock(DeliveryId::class),
            NotificationType::INFO,
            CommunicationChannel::EMAIL,
            $this->createMock(Recipient::class),
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