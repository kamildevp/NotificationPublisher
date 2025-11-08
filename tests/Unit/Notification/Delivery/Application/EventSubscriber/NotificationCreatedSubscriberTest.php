<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Application\EventSubscriber;

use App\Notification\Delivery\Application\EventSubscriber\NotificationCreatedSubscriber;
use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Event\DeliveryScheduledEvent;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Delivery\Domain\Service\NotificationDeliveryPolicyInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Delivery\Domain\ValueObject\DeliveryStatus;
use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotificationCreatedSubscriberTest extends TestCase
{
    private NotificationDeliveryPolicyInterface&MockObject $deliveryPolicyMock;
    private DeliveryRepositoryInterface&MockObject $deliveryRepositoryMock;
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private NotificationCreatedSubscriber $service;

    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->deliveryPolicyMock = $this->createMock(NotificationDeliveryPolicyInterface::class);
        $this->deliveryRepositoryMock = $this->createMock(DeliveryRepositoryInterface::class);
        $this->service = new NotificationCreatedSubscriber(
            $this->deliveryRepositoryMock,
            $this->eventDispatcherMock,
            new ArrayIterator([$this->deliveryPolicyMock]),
        );
    }
    
    public function testOnNotificationCreatedCreatesDeliveryForEachCommunicationChannelWhenDeliveryPoliciesPass(): void
    {
        $event = new NotificationCreatedEvent(
            new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $communicationChannels = CommunicationChannel::cases();

        $this->deliveryPolicyMock
            ->expects($this->exactly(count($communicationChannels)))
            ->method('canNotificationBeDelivered')
            ->willReturnMap(array_map(
                fn($communicationChannel) => [
                    $event->getNotificationRecipient(),
                    $event->getNotificationType(),
                    $event->getNotificationData(),
                    $communicationChannel,
                    true
                ],
                $communicationChannels
            ));

        $saveCallNr = 0;
        $this->deliveryRepositoryMock
            ->expects($this->exactly(count($communicationChannels)))
            ->method('save')
            ->with($this->callback(function($arg) use (&$saveCallNr, $event, $communicationChannels){
                $saveCallNr++;
                return $arg instanceof Delivery &&
                    $arg->getId() instanceof DeliveryId &&
                    $arg->getNotificationId() == $event->getNotificationId() &&
                    $arg->getNotificationType() == $event->getNotificationType() &&
                    $arg->getCommunicationChannel() == $communicationChannels[$saveCallNr-1] &&
                    $arg->getNotificationData() == $event->getNotificationData() &&
                    $arg->getRecipient() == $event->getNotificationRecipient() &&
                    $arg->getStatus() == DeliveryStatus::PENDING;
            }));

        $dispatchCallNr = 0;
        $this->eventDispatcherMock
            ->expects($this->exactly(count($communicationChannels)))
            ->method('dispatch')
            ->with($this->callback(function($arg) use (&$dispatchCallNr, $event, $communicationChannels){
                $dispatchCallNr++;
                return $arg instanceof DeliveryScheduledEvent &&
                    $arg->getDeliveryId() instanceof DeliveryId &&
                    $arg->getNotificationId() == $event->getNotificationId() &&
                    $arg->getNotificationType() == $event->getNotificationType() &&
                    $arg->getCommunicationChannel() == $communicationChannels[$dispatchCallNr-1] &&
                    $arg->getNotificationData() == $event->getNotificationData() &&
                    $arg->getNotificationRecipient() == $event->getNotificationRecipient();
            }));

        $this->service->onNotificationCreated($event);
    }

    public function testOnNotificationCreatedSkipsDeliveryCreationForUnavailableChannel(): void
    {
        $event = new NotificationCreatedEvent(
            new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b'),
            NotificationType::INFO,
            new Recipient('2a8045fd', new Email('user@example.com'), new Phone('+48213721372')),
            ['message' => 'test']
        );

        $communicationChannels = CommunicationChannel::cases();
        $unavailableChannel = CommunicationChannel::EMAIL;
        $availableChannels = array_values(array_filter($communicationChannels, fn($communicationChannel) => $communicationChannel != $unavailableChannel));

        $this->deliveryPolicyMock
            ->expects($this->exactly(count($communicationChannels)))
            ->method('canNotificationBeDelivered')
            ->willReturnMap(array_map(
                fn($communicationChannel) => [
                    $event->getNotificationRecipient(),
                    $event->getNotificationType(),
                    $event->getNotificationData(),
                    $communicationChannel,
                    $communicationChannel != $unavailableChannel
                ],
                $communicationChannels
            ));

        $saveCallNr = 0;
        $this->deliveryRepositoryMock
            ->expects($this->exactly(count($availableChannels)))
            ->method('save')
            ->with($this->callback(function($arg) use (&$saveCallNr, $event, $availableChannels){
                $saveCallNr++;
                return $arg instanceof Delivery &&
                    $arg->getId() instanceof DeliveryId &&
                    $arg->getNotificationId() == $event->getNotificationId() &&
                    $arg->getNotificationType() == $event->getNotificationType() &&
                    $arg->getCommunicationChannel() == $availableChannels[$saveCallNr-1] &&
                    $arg->getNotificationData() == $event->getNotificationData() &&
                    $arg->getRecipient() == $event->getNotificationRecipient() &&
                    $arg->getStatus() == DeliveryStatus::PENDING;
            }));

        $dispatchCallNr = 0;
        $this->eventDispatcherMock
            ->expects($this->exactly(count($availableChannels)))
            ->method('dispatch')
            ->with($this->callback(function($arg) use (&$dispatchCallNr, $event, $availableChannels){
                $dispatchCallNr++;
                return $arg instanceof DeliveryScheduledEvent &&
                    $arg->getDeliveryId() instanceof DeliveryId &&
                    $arg->getNotificationId() == $event->getNotificationId() &&
                    $arg->getNotificationType() == $event->getNotificationType() &&
                    $arg->getCommunicationChannel() == $availableChannels[$dispatchCallNr-1] &&
                    $arg->getNotificationData() == $event->getNotificationData() &&
                    $arg->getNotificationRecipient() == $event->getNotificationRecipient();
            }));

        $this->service->onNotificationCreated($event);
    }
}