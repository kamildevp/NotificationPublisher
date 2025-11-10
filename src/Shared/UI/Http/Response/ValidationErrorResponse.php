<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Response;

class ValidationErrorResponse extends ClientErrorResponse
{
    public const RESPONSE_STATUS = 422;
    public const RESPONSE_MESSAGE = 'Validation Error';

    /**
     * @param array<string, string> $headers
     */
    public function __construct(mixed $errors = null, array $headers = [])
    {
        parent::__construct(self::RESPONSE_STATUS, self::RESPONSE_MESSAGE, $errors, $headers);
    }
} 