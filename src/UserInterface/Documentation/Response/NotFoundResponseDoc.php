<?php

declare(strict_types=1);

namespace App\UserInterface\Documentation\Response;

use App\UserInterface\Http\Response\NotFoundResponse;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotFoundResponseDoc extends ClientErrorResponseDoc
{
    public function __construct(
        string $message = NotFoundResponse::RESPONSE_MESSAGE,
        string $description = 'Not Found Response',
        array $headers = [],
    )
    {
        parent::__construct(
            statusCode: NotFoundResponse::RESPONSE_STATUS,
            message: $message,
            description: $description,
            headers: $headers,
        );
    }
}