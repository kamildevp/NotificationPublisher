<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Response;

class BadRequestResponse extends ClientErrorResponse
{
    public const RESPONSE_STATUS = 400;
    public const RESPONSE_MESSAGE = 'Bad Request';

    /**
     * @param array<string, string> $headers
     */
    public function __construct(mixed $errors = null, array $headers = [])
    {
        parent::__construct(self::RESPONSE_STATUS, self::RESPONSE_MESSAGE, $errors, $headers);
    }
} 