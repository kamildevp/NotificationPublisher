<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Service;

use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;

interface CommunicationChannelSenderInterface
{
    public function supports(CommunicationChannel $channel): bool;

    public function send(Recipient $recipient, NotificationType $notificationType, array $notificationData): void;
}