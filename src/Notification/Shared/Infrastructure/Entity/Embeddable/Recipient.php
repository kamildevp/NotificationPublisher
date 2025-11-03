<?php

namespace App\Notification\Shared\Infrastructure\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Recipient
{
    #[ORM\Column(length: 255)]
    private string $customerIdentifier;

    #[ORM\Column(length: 255)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $phone;

    public function getCustomerIdentifier(): string
    {
        return $this->customerIdentifier;
    }

    public function setCustomerIdentifier(string $customerIdentifier): self
    {
        $this->customerIdentifier = $customerIdentifier;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }
}