<?php

declare(strict_types=1);

namespace App\Notification\Shared\Domain\ValueObject;

final class Recipient
{
    public function __construct(
        private string $customerIdentifier,
        private Email $email,
        private Phone $phone,
    )
    {
        
    }

    public function getCustomerIdentifier(): string
    {
        return $this->customerIdentifier;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }
}