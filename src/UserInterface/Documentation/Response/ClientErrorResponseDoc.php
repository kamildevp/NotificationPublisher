<?php

declare(strict_types=1);

namespace App\UserInterface\Documentation\Response;

use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ClientErrorResponseDoc extends OA\Response
{
    public function __construct(
        int $statusCode = 400, 
        ?string $message = null,
        ?string $description = null, 
        array $headers = [],
    )
    {
        $dataProperty =  new OA\Property(property: 'data', type: 'object', properties: [
                new OA\Property(property: "message", type: "string", example: $message),
                new OA\Property(property: 'errors', type: 'object'),
        ]);

        $content = new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "status", type: "string", example: 'fail'),
                $dataProperty
            ],
        );

        parent::__construct( 
            response: $statusCode,
            description: $description,
            content: $content,
            headers: $headers
        );
    }
}