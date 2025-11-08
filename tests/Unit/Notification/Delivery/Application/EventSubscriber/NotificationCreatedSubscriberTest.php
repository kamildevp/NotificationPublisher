<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Application\EventSubscriber;

use App\Notification\Delivery\Application\EventSubscriber\NotificationCreatedSubscriber;
use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Factory\DeliveryFactory;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Event\DomainEventInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificationCreatedSubscriberTest extends TestCase
{
    private DeliveryFactory&MockObject $deliveryFactoryMock;
    private DeliveryRepositoryInterface&MockObject $deliveryRepositoryMock;
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private NotificationCreatedSubscriber $service;

    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->deliveryFactoryMock = $this->createMock(DeliveryFactory::class);
        $this->deliveryRepositoryMock = $this->createMock(DeliveryRepositoryInterface::class);
        $this->service = new NotificationCreatedSubscriber(
            $this->deliveryRepositoryMock,
            $this->eventDispatcherMock,
            $this->deliveryFactoryMock,
        );
    }
    
    public function testOnNotificationCreatedSchedulesNotificationDeliveriesAndDispatchesEvents(): void
    {
        $event = new NotificationCreatedEvent(
            new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $eventMock = $this->createMock(DomainEventInterface::class);
        $deliveryMock = $this->createMock(Delivery::class);
        $deliveryMock
            ->expects($this->once())
            ->method('pullDomainEvents')
            ->willReturn([$eventMock]);

        $this->deliveryFactoryMock
            ->method('scheduleNotificationDeliveries')
            ->with(
                $event->getNotificationId(),
                $event->getNotificationRecipient(),
                $event->getNotificationType(),
                $event->getNotificationData(),
            )
            ->willReturn([$deliveryMock]);

        $this->deliveryRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($deliveryMock);

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($eventMock);

        $this->service->onNotificationCreated($event);
    }
}