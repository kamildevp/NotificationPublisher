<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    private mixed $rawData;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(mixed $data = null, int $statusCode = 200, array $headers = [])
    {
        $this->rawData = $data;
        parent::__construct($data, $statusCode, $headers);
    }

    public function getRawData(): mixed
    {
        return $this->rawData;
    }
} 