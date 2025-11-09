<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Response;

use Throwable;

interface ExceptionResponseInterface
{
    public static function createFromException(Throwable $throwable): ApiResponse;
} 