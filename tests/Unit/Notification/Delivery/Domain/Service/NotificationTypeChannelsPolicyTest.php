<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Domain\Service;

use App\Notification\Delivery\Domain\Config\AvailableChannelsConfig;
use App\Notification\Delivery\Domain\Service\NotificationTypeChannelsPolicy;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use PHPUnit\Framework\TestCase;

class NotificationTypeChannelsPolicyTest extends TestCase
{
    public function testCanNotificationBeDeliveredReturnsTrueWhenChannelAvailableForNotificationType(): void
    {
        $config = [NotificationType::ALERT->value => new AvailableChannelsConfig([CommunicationChannel::EMAIL->value])];
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $communicationChannel = CommunicationChannel::EMAIL;

        $notificationTypeChannelsPolicy = new NotificationTypeChannelsPolicy($config);
        $result = $notificationTypeChannelsPolicy->canNotificationBeDelivered($recipient, NotificationType::ALERT, $notificationData, $communicationChannel);

        $this->assertTrue($result);
    }

    public function testCanNotificationBeDeliveredReturnsFalseWhenChannelNotAvailableForNotificationType(): void
    {
        $config = [NotificationType::ALERT->value => new AvailableChannelsConfig([CommunicationChannel::SMS->value])];
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $communicationChannel = CommunicationChannel::EMAIL;

        $notificationTypeChannelsPolicy = new NotificationTypeChannelsPolicy($config);
        $result = $notificationTypeChannelsPolicy->canNotificationBeDelivered($recipient, NotificationType::ALERT, $notificationData, $communicationChannel);

        $this->assertFalse($result);
    }

    public function testCanNotificationBeDeliveredReturnsTrueWhenAvailableChannelsForNotificationTypeNotDefined(): void
    {
        $config = [NotificationType::INFO->value => new AvailableChannelsConfig([CommunicationChannel::EMAIL->value])];
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationData = ['message' => 'My message'];
        $communicationChannel = CommunicationChannel::EMAIL;

        $notificationTypeChannelsPolicy = new NotificationTypeChannelsPolicy($config);
        $result = $notificationTypeChannelsPolicy->canNotificationBeDelivered($recipient, NotificationType::ALERT, $notificationData, $communicationChannel);

        $this->assertTrue($result);
    }
}