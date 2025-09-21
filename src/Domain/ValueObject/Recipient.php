<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use DomainException;

class Recipient
{
    public function __construct(
        private string $identifier,
        private string $email,
        private string $phone,
    )
    {
        $this->validate($identifier, $email, $phone);
    }
    
    public function getIdentifier(): string
    {
        return $this->identifier;   
    }

    public function getEmail(): string
    {
        return $this->email;   
    }

    public function getPhone(): string
    {
        return $this->phone;   
    }

    protected function validate(string $identifier, string $email, string $phone): void
    {
        $validIdentifier = !empty($identifier);
        $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        $validPhone = !empty($phone);
        
        if(!$validIdentifier || !$validEmail || !$validPhone){
            throw new DomainException('Cannot create recipient from invalid values');
        }
    }
}