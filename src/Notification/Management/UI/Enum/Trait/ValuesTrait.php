<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Enum\Trait;

trait ValuesTrait
{
    /** @return (string|int)[] */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}