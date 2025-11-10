<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Management\Domain\Factory;

use App\Notification\Management\Domain\Factory\NotificationFactory;
use App\Notification\Management\Domain\Service\NotificationManagementPolicyInterface;
use App\Notification\Management\Domain\ValueObject\NotificationState;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use ArrayIterator;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NotificationFactoryTest extends TestCase
{
    private NotificationManagementPolicyInterface&MockObject $notificationManagementPolicyMock;
    private iterable $notificationManagementPoliciesMock;
    private NotificationFactory $service;

    protected function setUp(): void
    {
        $this->notificationManagementPolicyMock = $this->createMock(NotificationManagementPolicyInterface::class);
        $this->notificationManagementPoliciesMock = new ArrayIterator([$this->notificationManagementPolicyMock]);
        $this->service = new NotificationFactory(
            $this->notificationManagementPoliciesMock,
        );
    }

    public function testCreateReturnsScheduledNotificationWhenPoliciesPass(): void
    {
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationType = NotificationType::INFO;
        $notificationData = ['message' => 'My message'];

        $this->notificationManagementPolicyMock
            ->method('canNotificationBeSent')
            ->willReturn(true);

        $notification = $this->service->createNotification(
            $notificationId,
            $recipient,
            $notificationType,
            $notificationData
        );

        $this->assertEquals($notificationId, $notification->getId());
        $this->assertEquals($notificationType, $notification->getType());
        $this->assertEquals($notificationData, $notification->getData());
        $this->assertEquals($recipient, $notification->getRecipient());
        $this->assertEquals(NotificationState::SCHEDULED, $notification->getState());
        $this->assertInstanceOf(DateTimeImmutable::class, $notification->getCreatedAt());
    }

    public function testCreateReturnsDiscardedNotificationWhenPoliciesFail(): void
    {
        $notificationId = new NotificationId('242f924c-3910-45da-9291-772bcaa4fc6b');
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationType = NotificationType::INFO;
        $notificationData = ['message' => 'My message'];

        $this->notificationManagementPolicyMock
            ->method('canNotificationBeSent')
            ->willReturn(false);

        $notification = $this->service->createNotification(
            $notificationId,
            $recipient,
            $notificationType,
            $notificationData
        );

        $this->assertEquals($notificationId, $notification->getId());
        $this->assertEquals($notificationType, $notification->getType());
        $this->assertEquals($notificationData, $notification->getData());
        $this->assertEquals($recipient, $notification->getRecipient());
        $this->assertEquals(NotificationState::DISCARDED, $notification->getState());
        $this->assertInstanceOf(DateTimeImmutable::class, $notification->getCreatedAt());
    }
}