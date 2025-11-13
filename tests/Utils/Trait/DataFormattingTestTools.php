<?php

declare(strict_types=1);

namespace App\Tests\Utils\Trait;

trait DataFormattingTestTools
{
    /**
     * @param string[] $groups
     * @return mixed[]
     */
    protected function normalize(mixed $value, array $groups): array
    {
        return $this->normalizer->normalize($value, context: ['groups' => $groups]);
    }
}