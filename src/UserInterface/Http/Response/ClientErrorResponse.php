<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Response;

class ClientErrorResponse extends ApiResponse
{
    public const RESPONSE_STATUS = 400;

    public function __construct(int $statusCode = self::RESPONSE_STATUS, string $message = '', mixed $errors = null, array $headers = [])
    {
        parent::__construct([
            'status' => 'fail', 
            'data' => [
                'message' => $message,
                'errors' => $errors
            ]
        ], $statusCode, $headers);
    }
} 