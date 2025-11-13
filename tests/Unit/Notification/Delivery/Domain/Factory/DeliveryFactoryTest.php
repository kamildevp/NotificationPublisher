<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Domain\Factory;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Factory\DeliveryFactory;
use App\Notification\Delivery\Domain\Service\NotificationDeliveryPolicyInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Delivery\Domain\ValueObject\DeliveryStatus;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use ArrayIterator;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeliveryFactoryTest extends TestCase
{
    private UuidGeneratorInterface&MockObject $uuidGeneratorMock;
    private NotificationDeliveryPolicyInterface&MockObject $deliveryPolicyMock;
    private DeliveryFactory $service;

    protected function setUp(): void
    {
        $this->uuidGeneratorMock = $this->createMock(UuidGeneratorInterface::class);
        $this->deliveryPolicyMock = $this->createMock(NotificationDeliveryPolicyInterface::class);
        $this->service = new DeliveryFactory(
            $this->uuidGeneratorMock,
            new ArrayIterator([$this->deliveryPolicyMock]),
        );
    }
    
    public function testScheduleNotificationDeliveriesCreatesDeliveryForEachCommunicationChannelWhenDeliveryPoliciesPass(): void
    {
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $notificationType = NotificationType::ALERT;
        $communicationChannels = CommunicationChannel::cases();

        $this->deliveryPolicyMock
            ->expects($this->exactly(count($communicationChannels)))
            ->method('canNotificationBeDelivered')
            ->willReturnMap(array_map(
                fn($communicationChannel) => [
                    $recipient,
                    $notificationType,
                    $notificationData,
                    $communicationChannel,
                    true
                ],
                $communicationChannels
            ));

        $uuid = '242f9242-3910-45da-9291-772bcaa4fc6b';
        $this->uuidGeneratorMock
            ->method('generateUuid')
            ->willReturn($uuid);

        $deliveries = $this->service->scheduleNotificationDeliveries(
            $notificationId,
            $recipient,
            $notificationType,
            $notificationData
        );

        $this->assertCount(count($communicationChannels), $deliveries);
        foreach($deliveries as $delivery){
            $this->assertInstanceOf(Delivery::class, $delivery);
            $this->assertEquals($uuid, $delivery->getId()->getValue());
            $this->assertEquals($notificationId, $delivery->getNotificationId());
            $this->assertEquals($notificationType, $delivery->getNotificationType());
            $this->assertEquals($notificationData, $delivery->getNotificationData());
            $this->assertEquals($recipient, $delivery->getRecipient());
            $this->assertEquals(DeliveryStatus::PENDING, $delivery->getStatus());
            $this->assertInstanceOf(DateTimeImmutable::class, $delivery->getScheduledAt());
            $this->assertInstanceOf(CommunicationChannel::class, $delivery->getCommunicationChannel());
        }
    }

    public function testScheduleNotificationDeliveriesSkipsDeliveryCreationForUnavailableChannel(): void
    {
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $notificationType = NotificationType::ALERT;
        $communicationChannels = CommunicationChannel::cases();
        $unavailableChannel = CommunicationChannel::EMAIL;
        $availableChannels = array_values(array_filter($communicationChannels, fn($communicationChannel) => $communicationChannel != $unavailableChannel));


        $this->deliveryPolicyMock
            ->expects($this->exactly(count($communicationChannels)))
            ->method('canNotificationBeDelivered')
            ->willReturnMap(array_map(
                fn($communicationChannel) => [
                    $recipient,
                    $notificationType,
                    $notificationData,
                    $communicationChannel,
                    $communicationChannel != $unavailableChannel
                ],
                $communicationChannels
            ));

        $uuid = '242f9242-3910-45da-9291-772bcaa4fc6b';
        $this->uuidGeneratorMock
            ->method('generateUuid')
            ->willReturn($uuid);

        $deliveries = $this->service->scheduleNotificationDeliveries(
            $notificationId,
            $recipient,
            $notificationType,
            $notificationData
        );

        $this->assertCount(count($availableChannels), $deliveries);
        foreach($deliveries as $delivery){
            $this->assertInstanceOf(Delivery::class, $delivery);
            $this->assertEquals($uuid, $delivery->getId()->getValue());
            $this->assertEquals($notificationId, $delivery->getNotificationId());
            $this->assertEquals($notificationType, $delivery->getNotificationType());
            $this->assertEquals($notificationData, $delivery->getNotificationData());
            $this->assertEquals($recipient, $delivery->getRecipient());
            $this->assertEquals(DeliveryStatus::PENDING, $delivery->getStatus());
            $this->assertInstanceOf(DateTimeImmutable::class, $delivery->getScheduledAt());
            $this->assertInstanceOf(CommunicationChannel::class, $delivery->getCommunicationChannel());
            $this->assertTrue(in_array($delivery->getCommunicationChannel(), $availableChannels));
        }
    }
}