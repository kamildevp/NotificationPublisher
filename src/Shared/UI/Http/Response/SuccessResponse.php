<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Response;

class SuccessResponse extends ApiResponse
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(mixed $data = null, int $statusCode = 200, array $headers = [])
    {
        parent::__construct([
            'status' => 'success', 
            'data' => $data
        ], $statusCode, $headers);
    }
} 