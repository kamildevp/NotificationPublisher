<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Service\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;

class UuidGenerator implements UuidGeneratorInterface
{
    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}