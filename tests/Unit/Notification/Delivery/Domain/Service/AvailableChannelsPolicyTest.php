<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Domain\Service;

use App\Notification\Delivery\Domain\Config\AvailableChannelsConfig;
use App\Notification\Delivery\Domain\Service\AvailableChannelsPolicy;
use App\Notification\Shared\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use PHPUnit\Framework\TestCase;

class AvailableChannelsPolicyTest extends TestCase
{
    public function testCanNotificationBeDeliveredReturnsTrueWhenChannelAvailable(): void
    {
        $config = new AvailableChannelsConfig([CommunicationChannel::EMAIL->value]);
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $communicationChannel = CommunicationChannel::EMAIL;

        $availableChannelsPolicy = new AvailableChannelsPolicy($config);
        $result = $availableChannelsPolicy->canNotificationBeDelivered($recipient, NotificationType::ALERT, $notificationData, $communicationChannel);

        $this->assertTrue($result);
    }

    public function testCanNotificationBeDeliveredReturnsFalseWhenChannelNotAvailable(): void
    {
        $config = new AvailableChannelsConfig([CommunicationChannel::SMS->value]);
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $communicationChannel = CommunicationChannel::EMAIL;

        $availableChannelsPolicy = new AvailableChannelsPolicy($config);
        $result = $availableChannelsPolicy->canNotificationBeDelivered($recipient, NotificationType::ALERT, $notificationData, $communicationChannel);

        $this->assertFalse($result);
    }
}