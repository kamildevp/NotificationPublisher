<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\Factory;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Service\NotificationDeliveryPolicyInterface;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use App\Shared\Domain\Service\UuidGeneratorInterface;

class DeliveryFactory
{
    /**
     * @param iterable<NotificationDeliveryPolicyInterface> $notificationDeliveryPolices
     */
    public function __construct(
        private UuidGeneratorInterface $uuidGenerator,
        private iterable $notificationDeliveryPolices,
    )
    {
        
    }

    public function scheduleNotificationDeliveries(
        NotificationId $notificationId,
        Recipient $recipient, 
        NotificationType $notificationType, 
        array $notificationData, 
    ): array
    {
        $deliveries = [];
        foreach(CommunicationChannel::cases() as $communicationChannel)
        {
            if(!$this->validateDeliveryPolicies(
                $recipient, 
                $notificationType, 
                $notificationData, 
                $communicationChannel
            )){
                continue;
            }

            $deliveryId = new DeliveryId($this->uuidGenerator->generateUuid());
            $delivery = Delivery::schedule(
                $deliveryId,
                $notificationId,
                $notificationType,
                $communicationChannel,
                $notificationData,
                $recipient
            );

            $deliveries[] = $delivery;
        }

        return $deliveries;
    }

    private function validateDeliveryPolicies(
        Recipient $recipient, 
        NotificationType $notificationType, 
        array $notificationData, 
        CommunicationChannel $communicationChannel
    ): bool
    {
        foreach($this->notificationDeliveryPolices as $notificationDeliveryPolicy)
        {
            if(!$notificationDeliveryPolicy->canNotificationBeDelivered(
                $recipient, 
                $notificationType, 
                $notificationData, 
                $communicationChannel
            )){
                return false;
            }
        }
            
        return true;
    }
}