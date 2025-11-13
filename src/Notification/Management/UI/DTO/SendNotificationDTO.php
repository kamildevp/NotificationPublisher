<?php 

declare(strict_types=1);

namespace App\Notification\Management\UI\DTO;

use App\Notification\Management\UI\Enum\NotificationType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Notification\Management\UI\Validator\Constraints as CustomAssert;
use OpenApi\Attributes as OA;

#[CustomAssert\NotificationData]
class SendNotificationDTO
{
    /** @param mixed[] $notificationData */
    public function __construct(
        #[Assert\Valid]
        private RecipientDTO $recipient,
        #[Assert\Choice(callback: [NotificationType::class, 'values'])]
        private string $notificationType,
        #[OA\Property(type: 'object')]
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

    /** @return mixed[] */
    public function getNotificationData(): array
    {
        return $this->notificationData;   
    }
}