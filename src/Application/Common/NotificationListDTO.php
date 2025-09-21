<?php 

declare(strict_types=1);

namespace App\Application\Common;

class NotificationListDTO
{
    public function __construct(
        private int $page = 1,
        private int $perPage = 20,
        private ?string $recipientIdentifier = null,
    )
    {

    }

    public function getPage(): int
    {
        return $this->page;   
    }

    public function getPerPage(): int
    {
        return $this->perPage;   
    }

    public function getRecipientIdentifier(): ?string
    {
        return $this->recipientIdentifier;   
    }
}