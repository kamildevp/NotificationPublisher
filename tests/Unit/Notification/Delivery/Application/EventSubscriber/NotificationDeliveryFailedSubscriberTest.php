<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Application\EventSubscriber;

use App\Notification\Delivery\Application\Command\DeliverNotificationCommand;
use App\Notification\Delivery\Application\EventSubscriber\NotificationDeliveryFailedSubscriber;
use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Event\DeliveryFailedEvent;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Delivery\Domain\Service\DeliveryRetryPolicy;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class NotificationDeliveryFailedSubscriberTest extends TestCase
{
    private DeliveryRetryPolicy&MockObject $deliveryRetryPolicyMock;
    private DeliveryRepositoryInterface&MockObject $deliveryRepositoryMock;
    private MessageBusInterface&MockObject $messageBusMock;
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private NotificationDeliveryFailedSubscriber $service;

    protected function setUp(): void
    {
        $this->messageBusMock = $this->createMock(MessageBusInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->deliveryRetryPolicyMock = $this->createMock(DeliveryRetryPolicy::class);
        $this->deliveryRepositoryMock = $this->createMock(DeliveryRepositoryInterface::class);
        $this->service = new NotificationDeliveryFailedSubscriber(
            $this->messageBusMock,
            $this->eventDispatcherMock,
            $this->deliveryRetryPolicyMock,
            $this->deliveryRepositoryMock,
        );
    }
    
    public function testOnNotificationDeliveryFailedRetriesDeliveryWhenShouldRetry(): void
    {
        $command = new DeliverNotificationCommand(
            new DeliveryId('242f924c-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            CommunicationChannel::EMAIL,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $event = new WorkerMessageFailedEvent(
            new Envelope($command),
            'test',
            $this->createMock(Exception::class)
        );
        $retryDelay = 1000;

        $this->deliveryRetryPolicyMock
            ->expects($this->once())
            ->method('shouldRetry')
            ->with($command->getAttemptNr())
            ->willReturn(true);

        $this->deliveryRetryPolicyMock
            ->expects($this->once())
            ->method('getRetryDelay')
            ->with($command->getAttemptNr())
            ->willReturn($retryDelay);

        $this->messageBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn($arg) => 
                    $arg instanceof DeliverNotificationCommand &&
                    $arg->getDeliveryId() == $command->getDeliveryId() &&
                    $arg->getNotificationType() == $command->getNotificationType() &&
                    $arg->getCommunicationChannel() == $command->getCommunicationChannel() &&
                    $arg->getNotificationRecipient() == $command->getNotificationRecipient() &&
                    $arg->getNotificationData() == $command->getNotificationData() &&
                    $arg->getAttemptNr() == 2
                ),
                $this->callback(fn($arg) => 
                    is_array($arg) &&
                    count($arg) == 1 &&
                    $arg[0] instanceof DelayStamp &&
                    $arg[0]->getDelay() == $retryDelay
                )
            )
            ->willReturn(new Envelope($this->createMock(DeliverNotificationCommand::class)));

        $this->service->onNotificationDeliveryFailed($event);
    }

    public function testOnNotificationDeliveryFailedMarksDeliveryAsFailedWhenShouldNotRetry(): void
    {
        $command = new DeliverNotificationCommand(
            new DeliveryId('242f924c-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            CommunicationChannel::EMAIL,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $event = new WorkerMessageFailedEvent(
            new Envelope($command),
            'test',
            $this->createMock(Exception::class)
        );

        $this->deliveryRetryPolicyMock
            ->expects($this->once())
            ->method('shouldRetry')
            ->with($command->getAttemptNr())
            ->willReturn(false);

        $deliveryFailedEvent = new DeliveryFailedEvent(
            $command->getDeliveryId()
        );

        $deliveryMock = $this->createMock(Delivery::class);
        $deliveryMock
            ->method('pullDomainEvents')
            ->willReturn([$deliveryFailedEvent]);
        $deliveryMock
            ->expects($this->once())
            ->method('markFailed');

        $this->deliveryRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($command->getDeliveryId())
            ->willReturn($deliveryMock);

        $this->deliveryRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($deliveryMock);

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($deliveryFailedEvent);

        $this->service->onNotificationDeliveryFailed($event);
    }
}