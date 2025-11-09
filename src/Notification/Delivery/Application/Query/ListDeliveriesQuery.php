<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Application\Query;

class ListDeliveriesQuery
{
    public function __construct(
        private int $page = 1,
        private int $perPage = 20,
        private ?string $recipientCustomerIdentifier = null,
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

    public function getRecipientCustomerIdentifier(): ?string
    {
        return $this->recipientCustomerIdentifier;
    }
}