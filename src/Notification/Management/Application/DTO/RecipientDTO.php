<?php 

declare(strict_types=1);

namespace App\Notification\Management\Application\DTO;

class RecipientDTO
{
    public function __construct(
        private string $customerIdentifier,
        private string $email,
        private string $phone,
    )
    {

    }

    public function getCustomerIdentifier(): string
    {
        return $this->customerIdentifier;   
    }

    public function getEmail(): string
    {
        return $this->email;   
    }

    public function getPhone(): string
    {
        return $this->phone;   
    }
}