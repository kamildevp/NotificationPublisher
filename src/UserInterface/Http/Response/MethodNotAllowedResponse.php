<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Response;

class MethodNotAllowedResponse extends ApiResponse
{
    public const RESPONSE_STATUS = 405;
    public const RESPONSE_MESSAGE = 'Method not allowed';

    public function __construct(string $message = self::RESPONSE_MESSAGE, array $headers = [])
    {
        parent::__construct([
            'status' => 'fail', 
            'data' => [
                'message' => $message,
            ]
        ], self:: RESPONSE_STATUS, $headers);
    }
} 