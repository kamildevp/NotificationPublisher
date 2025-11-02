<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

use InvalidArgumentException;

abstract class EmailValueObject
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->ensureIsValidEmail($value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function ensureIsValidEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(sprintf('The value <%s> is not valid email', $email));
        }
    }
}
