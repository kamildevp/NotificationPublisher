<?php 

declare(strict_types=1);

namespace App\Notification\Management\UI\DTO;

use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class RecipientDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        private string $customerIdentifier,
        #[OA\Property(example: 'user@example.com')]
        #[Assert\NotBlank]
        #[Assert\Email]
        private string $email,
        #[Assert\NotBlank]
        #[PhoneNumber]
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