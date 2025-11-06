<?php

declare(strict_types=1);

namespace App\Notification\Shared\Infrastructure\Doctrine\Type;

use App\Notification\Shared\Domain\ValueObject\Email;
use App\Shared\Infrastructure\Doctrine\Type\EmailValueObjectType;

final class EmailType extends EmailValueObjectType
{
    public function getName(): string
    {
        return 'email';
    }

    protected function typeClassName(): string
    {
        return Email::class;
    }
}
