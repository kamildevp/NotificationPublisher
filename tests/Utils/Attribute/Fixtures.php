<?php

declare(strict_types=1);

namespace App\Tests\Utils\Attribute;

use Attribute;

#[\Attribute(Attribute::TARGET_METHOD)]
class Fixtures
{
    /**
     * @param class-string[] $fixtures
     */
    public function __construct(public array $fixtures)
    {
        
    }
}