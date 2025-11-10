<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Response;

class NotFoundResponse extends ClientErrorResponse
{
    public const RESPONSE_STATUS = 404;
    public const RESPONSE_MESSAGE = 'Not Found';

    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $message = self::RESPONSE_MESSAGE, array $headers = [])
    {
        parent::__construct(self::RESPONSE_STATUS, $message, null, $headers);
    }
} 