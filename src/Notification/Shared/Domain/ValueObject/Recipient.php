<?php

declare(strict_types=1);

namespace App\Notification\Shared\Domain\ValueObject;

class Recipient
{
    public function __construct(
        private string $customerIdentifier,
        private Email $email,
        private Phone $phone,
    )
    {
        
    }
}