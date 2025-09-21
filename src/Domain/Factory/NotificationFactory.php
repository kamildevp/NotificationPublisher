<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Entity\Notification;
use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Enum\NotificationType;
use App\Domain\Event\NotificationCreatedEvent;
use App\Domain\Policy\AvailableChannelsPolicy;
use App\Domain\Policy\RatePolicy;
use App\Domain\ValueObject\Recipient;
use DateTimeImmutable;
use DomainException;

class NotificationFactory
{
    public function __construct(
        private AvailableChannelsPolicy $availableChannelsPolicy,
        private RatePolicy $ratePolicy
    )
    {
        
    }

    public function create(NotificationType $type, string $message, Recipient $recipient, Channel $channel, int $recipientRecentNotificationsCount): Notification
    {
        if(
            !$this->availableChannelsPolicy->isAvailable($channel) || 
            $this->ratePolicy->isRateLimitExceeded($type, $recipientRecentNotificationsCount)
        ){
            throw new DomainException('Notification cannot be created');
        }

        $notification = new Notification();
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setRecipientIdentifier($recipient->getIdentifier());
        $notification->setChannel($channel);
        $notification->setStatus(NotificationStatus::PENDING);
        $notification->setCreatedAt(new DateTimeImmutable());
        $notification->recordEvent(new NotificationCreatedEvent(
            $recipient,
            $message,
            $channel,
            $notification->getId(),
            $type
        ));

        return $notification;
    }
}