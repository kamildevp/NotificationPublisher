<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Service;

use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;

interface NotificationDeliveryPolicyInterface
{
    /** @param mixed[] $notificationData */
    public function canNotificationBeDelivered(
        Recipient $recipient, 
        NotificationType $notificationType, 
        array $notificationData, 
        CommunicationChannel $communicationChannel
    ): bool;
}