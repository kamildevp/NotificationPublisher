<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Response;

use Throwable;

interface ExceptionResponseInterface
{
    public static function createFromException(Throwable $throwable): ApiResponse;
} 