<?php 

declare(strict_types=1);

namespace App\Notification\Management\UI\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AlertNotificationDataDTO
{
    public function __construct(
        #[Assert\NotBlank]
        private string $message,
    )
    {

    }

    public function getMessage(): string
    {
        return $this->message;   
    }
}