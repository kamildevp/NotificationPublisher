<?php

declare(strict_types=1);

namespace App\Tests\Domain\Factory;

use App\Domain\Entity\Notification;
use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationType;
use App\Domain\Factory\NotificationFactory;
use App\Domain\Policy\AvailableChannelsPolicy;
use App\Domain\Policy\RatePolicy;
use App\Domain\ValueObject\Recipient;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NotificationFactoryTest extends TestCase
{
    private AvailableChannelsPolicy&MockObject $availableChannelsPolicyMock;
    private RatePolicy&MockObject $ratePolicyMock;
    private NotificationFactory $factory;

    protected function setUp(): void
    {
        $this->availableChannelsPolicyMock = $this->createMock(AvailableChannelsPolicy::class);
        $this->ratePolicyMock = $this->createMock(RatePolicy::class);

        $this->factory = new NotificationFactory(
            $this->availableChannelsPolicyMock,
            $this->ratePolicyMock
        );
    }

    public function testCreateNotificationSuccessfully(): void
    {
        $recipient = new Recipient('user-123', 'user@example.com', '1234567890');
        $type = NotificationType::ALERT;
        $channel = Channel::EMAIL;
        $message = 'Test notification';

        $this->availableChannelsPolicyMock->method('isAvailable')->with($channel)->willReturn(true);
        $this->ratePolicyMock->method('isRateLimitExceeded')->with($type, 0)->willReturn(false);

        $notification = $this->factory->create($type, $message, $recipient, $channel, 0);

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame($type, $notification->getType());
        $this->assertSame($recipient->getIdentifier(), $notification->getRecipientIdentifier());
        $this->assertCount(1, $notification->popEvents());
    }

    public function testCreateNotificationThrowsWhenChannelUnavailable(): void
    {
        $recipient = new Recipient('user-123', 'user@example.com', '1234567890');
        $type = NotificationType::ALERT;
        $channel = Channel::EMAIL;
        $message = 'Test notification';

        $this->availableChannelsPolicyMock->method('isAvailable')->with($channel)->willReturn(false);
        $this->ratePolicyMock->method('isRateLimitExceeded')->with($type, 0)->willReturn(false);

        $this->expectException(DomainException::class);

        $this->factory->create($type, $message, $recipient, $channel, 0);
    }

    public function testCreateNotificationThrowsWhenRateLimitExceeded(): void
    {
        $recipient = new Recipient('user-123', 'user@example.com', '1234567890');
        $type = NotificationType::ALERT;
        $channel = Channel::EMAIL;
        $message = 'Test notification';

        $this->availableChannelsPolicyMock->method('isAvailable')->with($channel)->willReturn(true);
        $this->ratePolicyMock->method('isRateLimitExceeded')->with($type, 5)->willReturn(true);

        $this->expectException(DomainException::class);

        $this->factory->create($type, $message, $recipient, $channel, 5);
    }
}
