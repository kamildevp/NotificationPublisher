<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Management\Domain\Entity;

use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use App\Notification\Management\Domain\Event\NotificationDiscardedEvent;
use App\Notification\Management\Domain\ValueObject\NotificationState;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testNotificationCanBeCreated(): void
    {
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $notification = Notification::create($notificationId, NotificationType::ALERT, $notificationData, $recipient);

        $this->assertEquals($notificationId, $notification->getId());
        $this->assertEquals(NotificationType::ALERT, $notification->getType());
        $this->assertEquals($notificationData, $notification->getData());
        $this->assertEquals($recipient, $notification->getRecipient());
        $this->assertEquals(NotificationState::SCHEDULED, $notification->getState());
        $this->assertInstanceOf(DateTimeImmutable::class, $notification->getCreatedAt());
        
        $events = $notification->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(NotificationCreatedEvent::class, $events[0]);
        /** @var NotificationCreatedEvent */
        $event = $events[0];
        $this->assertEquals($notificationId, $event->getNotificationId());
        $this->assertEquals(NotificationType::ALERT, $event->getNotificationType());
        $this->assertEquals($notificationData, $event->getNotificationData());
        $this->assertEquals($recipient, $event->getNotificationRecipient());
    }

    public function testNotificationCanBeDiscarded(): void
    {
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $notification = Notification::discard($notificationId, NotificationType::ALERT, $notificationData, $recipient);

        $this->assertEquals($notificationId, $notification->getId());
        $this->assertEquals(NotificationType::ALERT, $notification->getType());
        $this->assertEquals($notificationData, $notification->getData());
        $this->assertEquals($recipient, $notification->getRecipient());
        $this->assertEquals(NotificationState::DISCARDED, $notification->getState());
        $this->assertInstanceOf(DateTimeImmutable::class, $notification->getCreatedAt());
        
        $events = $notification->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(NotificationDiscardedEvent::class, $events[0]);
        /** @var NotificationDiscardedEvent */
        $event = $events[0];
        $this->assertEquals($notificationId, $event->getNotificationId());
        $this->assertEquals(NotificationType::ALERT, $event->getNotificationType());
        $this->assertEquals($notificationData, $event->getNotificationData());
        $this->assertEquals($recipient, $event->getNotificationRecipient());
    }
}