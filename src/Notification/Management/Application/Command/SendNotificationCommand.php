<?php

declare(strict_types=1);

namespace App\Notification\Management\Application\Command;

use App\Notification\Management\Application\DTO\RecipientDTO;

class SendNotificationCommand
{
    public function __construct(
        private RecipientDTO $recipient,
        private string $notificationType,
        private array $notificationData,
    )
    {
        
    }

    public function getRecipient(): RecipientDTO
    {
        return $this->recipient;
    }

    public function getNotificationType(): string
    {
        return $this->notificationType;
    }

    public function getNotificationData(): array
    {
        return $this->notificationData;
    }
}