<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use App\Domain\Enum\Trait\ValuesTrait;

enum Channel: string
{
    use ValuesTrait;

    case EMAIL = 'email';
    case SMS = 'sms';
}