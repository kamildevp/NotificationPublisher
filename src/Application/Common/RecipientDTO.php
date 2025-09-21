<?php 

declare(strict_types=1);

namespace App\Application\Common;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class RecipientDTO
{
    #[Assert\NotBlank]
    private string $identifier;

    #[OA\Property(example: 'user@example.com')]
    #[Assert\Email]
    private string $email;

    #[OA\Property(example: '+48013051133')]
    #[Assert\NotBlank]
    private string $phone;

    public function __construct(
        string $identifier,
        string $email,
        string $phone,
    )
    {
        $this->identifier = $identifier;
        $this->email = $email;
        $this->phone = $phone;
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
}