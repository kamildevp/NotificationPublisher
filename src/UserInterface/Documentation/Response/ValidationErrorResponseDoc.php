<?php

declare(strict_types=1);

namespace App\UserInterface\Documentation\Response;

use App\UserInterface\Http\Response\ValidationErrorResponse;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ValidationErrorResponseDoc extends ClientErrorResponseDoc
{
    public function __construct(
        string $description = 'Validation Error Response',  
        array $headers = []
    )
    {
        parent::__construct(
            statusCode: ValidationErrorResponse::RESPONSE_STATUS,
            message: ValidationErrorResponse::RESPONSE_MESSAGE,
            description: $description,
            headers: $headers
        );
    }
}