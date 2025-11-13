<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Management\Domain\Service;

use App\Notification\Management\Domain\Config\RateConfig;
use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use App\Notification\Management\Domain\Service\RatePolicy;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RatePolicyTest extends TestCase
{
    private NotificationRepositoryInterface&MockObject $notificationRepositoryMock;

    protected function setUp(): void
    {
        $this->notificationRepositoryMock = $this->createMock(NotificationRepositoryInterface::class);
    }

    public function testCanNotificationBeSentReturnsTrueWhenRateLimitNotExceeded(): void
    {
        $config = [NotificationType::ALERT->value => new RateConfig(30)];

        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];

        $this->notificationRepositoryMock
            ->method('getRecipientNotificationCount')
            ->with(
                $recipient,
                NotificationType::ALERT,
                $this->isInstanceOf(DateTimeInterface::class),
                $this->isInstanceOf(DateTimeInterface::class),
            )
            ->willReturn(20);

        $ratePolicy = new RatePolicy($this->notificationRepositoryMock, $config);
        $result = $ratePolicy->canNotificationBeSent($recipient, NotificationType::ALERT, $notificationData);

        $this->assertTrue($result);
    }

    public function testCanNotificationBeSentReturnsFalseWhenRateLimitExceeded(): void
    {
        $config = [NotificationType::ALERT->value => new RateConfig(30)];

        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];

        $this->notificationRepositoryMock
            ->method('getRecipientNotificationCount')
            ->with(
                $recipient,
                NotificationType::ALERT,
                $this->isInstanceOf(DateTimeInterface::class),
                $this->isInstanceOf(DateTimeInterface::class),
            )
            ->willReturn(40);

        $ratePolicy = new RatePolicy($this->notificationRepositoryMock, $config);
        $result = $ratePolicy->canNotificationBeSent($recipient, NotificationType::ALERT, $notificationData);

        $this->assertFalse($result);
    }

    public function testCanNotificationBeSentReturnsTrueWhenRateLimitNotDefined(): void
    {
        $config = [NotificationType::ALERT->value => new RateConfig(30)];

        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];

        $this->notificationRepositoryMock
            ->expects($this->never())
            ->method('getRecipientNotificationCount');

        $ratePolicy = new RatePolicy($this->notificationRepositoryMock, $config);
        $result = $ratePolicy->canNotificationBeSent($recipient, NotificationType::INFO, $notificationData);

        $this->assertTrue($result);
    }
}