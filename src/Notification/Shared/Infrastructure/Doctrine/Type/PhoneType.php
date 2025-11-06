<?php

declare(strict_types=1);

namespace App\Notification\Shared\Infrastructure\Doctrine\Type;

use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Shared\Infrastructure\Doctrine\Type\PhoneValueObjectType;

final class PhoneType extends PhoneValueObjectType
{
    public function getName(): string
    {
        return 'phone';
    }

    protected function typeClassName(): string
    {
        return Phone::class;
    }
}
