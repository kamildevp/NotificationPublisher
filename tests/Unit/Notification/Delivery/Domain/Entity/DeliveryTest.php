<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Domain\Entity;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Event\DeliveryCompletedEvent;
use App\Notification\Delivery\Domain\Event\DeliveryFailedEvent;
use App\Notification\Delivery\Domain\Event\DeliveryScheduledEvent;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Delivery\Domain\ValueObject\DeliveryStatus;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeliveryTest extends TestCase
{
    public function testNotificationDeliveryCanBeScheduled(): void
    {
        $deliveryId = new DeliveryId('242f9242-3910-45da-9291-772bcaa4fc6b');
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $delivery = Delivery::schedule(
            $deliveryId,
            $notificationId, 
            NotificationType::ALERT, 
            CommunicationChannel::EMAIL, 
            $notificationData, 
            $recipient
        );

        $this->assertEquals($deliveryId, $delivery->getId());
        $this->assertEquals($notificationId, $delivery->getNotificationId());
        $this->assertEquals(NotificationType::ALERT, $delivery->getNotificationType());
        $this->assertEquals($notificationData, $delivery->getNotificationData());
        $this->assertEquals(CommunicationChannel::EMAIL, $delivery->getCommunicationChannel());
        $this->assertEquals($recipient, $delivery->getRecipient());
        $this->assertEquals(DeliveryStatus::PENDING, $delivery->getStatus());
        $this->assertInstanceOf(DateTimeImmutable::class, $delivery->getScheduledAt());
        $this->assertNull($delivery->getCompletedAt());
        
        $events = $delivery->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DeliveryScheduledEvent::class, $events[0]);
        /** @var DeliveryScheduledEvent */
        $event = $events[0];
        $this->assertEquals($deliveryId, $event->getDeliveryId());
        $this->assertEquals(NotificationType::ALERT, $event->getNotificationType());
        $this->assertEquals(CommunicationChannel::EMAIL, $event->getCommunicationChannel());
        $this->assertEquals($notificationData, $event->getNotificationData());
        $this->assertEquals($recipient, $event->getNotificationRecipient());
    }

    public function testNotificationDeliveryCanBeMarkedAsCompleted(): void
    {
        $deliveryId = new DeliveryId('242f9242-3910-45da-9291-772bcaa4fc6b');
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $delivery = Delivery::schedule(
            $deliveryId,
            $notificationId, 
            NotificationType::ALERT, 
            CommunicationChannel::EMAIL, 
            $notificationData, 
            $recipient
        );
        $delivery->pullDomainEvents();

        $delivery->markCompleted();
        $this->assertEquals(DeliveryStatus::COMPLETED, $delivery->getStatus());
        $this->assertInstanceOf(DateTimeImmutable::class, $delivery->getCompletedAt());
        
        $events = $delivery->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DeliveryCompletedEvent::class, $events[0]);
        /** @var DeliveryCompletedEvent */
        $event = $events[0];
        $this->assertEquals($deliveryId, $event->getDeliveryId());
    }

    public function testNotificationDeliveryCanBeMarkedAsFailed(): void
    {
        $deliveryId = new DeliveryId('242f9242-3910-45da-9291-772bcaa4fc6b');
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $delivery = Delivery::schedule(
            $deliveryId,
            $notificationId, 
            NotificationType::ALERT, 
            CommunicationChannel::EMAIL, 
            $notificationData, 
            $recipient
        );
        $delivery->pullDomainEvents();

        $delivery->markFailed();
        $this->assertEquals(DeliveryStatus::FAILED, $delivery->getStatus());
        $this->assertNull($delivery->getCompletedAt());
        
        $events = $delivery->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DeliveryFailedEvent::class, $events[0]);
        /** @var DeliveryFailedEvent */
        $event = $events[0];
        $this->assertEquals($deliveryId, $event->getDeliveryId());
    }
}