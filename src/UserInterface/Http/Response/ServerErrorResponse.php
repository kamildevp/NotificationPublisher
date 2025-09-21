<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Response;

class ServerErrorResponse extends ApiResponse
{
    public const RESPONSE_STATUS = 500;
    public const RESPONSE_MESSAGE = 'Server Error';

    public function __construct(
        string $message = self::RESPONSE_MESSAGE, 
        int $statusCode = 500, 
        mixed $data = null, 
        ?int $errorCode = null, 
        array $headers = []
        )
    {
        parent::__construct([
            'status' => 'error', 
            'message' => $message, 
            'data' => $data,
            'code' => $errorCode 
        ], $statusCode, $headers);
    }
} 