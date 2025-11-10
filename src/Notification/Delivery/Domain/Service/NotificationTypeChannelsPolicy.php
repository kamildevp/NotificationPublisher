<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Service;

use App\Notification\Delivery\Domain\Config\AvailableChannelsConfig;
use App\Notification\Delivery\Domain\Service\NotificationDeliveryPolicyInterface;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use InvalidArgumentException;

class NotificationTypeChannelsPolicy implements NotificationDeliveryPolicyInterface
{
    /** @param array<string,AvailableChannelsConfig> $config */
    public function __construct(
        private array $config,
    )
    {

    }

    /** @param mixed[] $notificationData */
    public function canNotificationBeDelivered(
        Recipient $recipient, 
        NotificationType $notificationType, 
        array $notificationData, 
        CommunicationChannel $communicationChannel
    ): bool
    {
        $availableChannelsConfig = $this->config[$notificationType->value] ?? null;
        if(!$availableChannelsConfig){
            return true;
        }

        /** @phpstan-ignore-next-line */
        if(!$availableChannelsConfig instanceof AvailableChannelsConfig)
        {
            throw new InvalidArgumentException(sprintf('Expected %s for notification type %s config, got %s', AvailableChannelsConfig::class, $notificationType->value, get_debug_type($availableChannelsConfig)));
        }

        return in_array($communicationChannel->value, $availableChannelsConfig->channels);
    }
}