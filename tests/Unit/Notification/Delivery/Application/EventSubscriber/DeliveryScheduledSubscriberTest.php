<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Application\EventSubscriber;

use App\Notification\Delivery\Application\Command\DeliverNotificationCommand;
use App\Notification\Delivery\Application\EventSubscriber\DeliveryScheduledSubscriber;
use App\Notification\Delivery\Domain\Event\DeliveryScheduledEvent;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class DeliveryScheduledSubscriberTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBusMock;
    private DeliveryScheduledSubscriber $service;

    protected function setUp(): void
    {
        $this->messageBusMock = $this->createMock(MessageBusInterface::class);
        $this->service = new DeliveryScheduledSubscriber(
            $this->messageBusMock,
        );
    }
    
    public function testOnDeliveryScheduledDispatchesDeliverNotificationCommand(): void
    {
        $event = new DeliveryScheduledEvent(
            new DeliveryId('242f924c-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            CommunicationChannel::EMAIL,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $this->messageBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn($arg) => 
                $arg instanceof DeliverNotificationCommand &&
                $arg->getDeliveryId() == $event->getDeliveryId() &&
                $arg->getNotificationType() == $event->getNotificationType() &&
                $arg->getCommunicationChannel() == $event->getCommunicationChannel() &&
                $arg->getNotificationRecipient() == $event->getNotificationRecipient() &&
                $arg->getNotificationData() == $event->getNotificationData()
            ))
            ->willReturn(new Envelope($this->createMock(DeliverNotificationCommand::class)));

        $this->service->onDeliveryScheduled($event);
    }
}