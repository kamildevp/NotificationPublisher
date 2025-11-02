<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

use InvalidArgumentException;

abstract class PhoneValueObject
{
    public function __construct(protected string $value)
    {
        $this->ensureIsValidPhone($value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function ensureIsValidPhone(string $phone): void
    {
        if(!preg_match('/^\+\d{6,15}$/', $phone)){
            throw new InvalidArgumentException(sprintf('The value <%s> is not valid phone', $phone));
        }
    }
}
