<?php

declare(strict_types=1);

namespace App\Notification\Management\Domain\Factory;

use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;

class NotificationFactory
{
    /**
     * @param iterable<NotificationManagementPolicyInterface> $notificationPolicies
     */
    public function __construct(
        private iterable $notificationManagementPolicies,
    )
    {
        
    }

    public function createNotification(
        NotificationId $notificationId,
        Recipient $recipient,
        NotificationType $notificationType,
        array $notificationData,
    ): Notification
    {
        $policiesPassed = true;
        foreach($this->notificationManagementPolicies as $policy){
            if(!$policy->canNotificationBeSent($recipient, $notificationType, $notificationData)){
                $policiesPassed = false;
                break;
            }
        }

        if($policiesPassed){
            $notification = Notification::create($notificationId, $notificationType, $notificationData, $recipient);
        }
        else{
            $notification = Notification::discard($notificationId, $notificationType, $notificationData, $recipient);
        }

        return $notification;
    }
}