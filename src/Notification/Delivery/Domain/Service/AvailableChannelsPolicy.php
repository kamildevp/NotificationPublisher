<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Service;

use App\Notification\Delivery\Domain\Config\AvailableChannelsConfig;
use App\Notification\Delivery\Domain\Service\NotificationDeliveryPolicyInterface;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;

class AvailableChannelsPolicy implements NotificationDeliveryPolicyInterface
{
    public function __construct(
        private AvailableChannelsConfig $config,
    )
    {

    }

    public function canNotificationBeDelivered(
        Recipient $recipient, 
        NotificationType $notificationType, 
        array $notificationData, 
        CommunicationChannel $communicationChannel
    ): bool
    {
        return in_array($communicationChannel->value, $this->config->channels);
    }
}