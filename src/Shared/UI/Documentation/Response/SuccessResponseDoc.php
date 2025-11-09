<?php

declare(strict_types=1);

namespace App\Shared\UI\Documentation\Response;

use OpenApi\Attributes as OA;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class SuccessResponseDoc extends OA\Response
{
    public function __construct(
        int $statusCode = 200, 
        ?string $description = 'Success Response', 
        ?OA\Schema $dataSchema = null, 
        mixed $dataExample = Generator::UNDEFINED,
        array $headers = []
    )
    {
        $dataProperty = new OA\Property(property: 'data', type: $dataSchema?->type, properties: $dataSchema?->properties, example: $dataExample);
        $content = new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "status", type: "string", example: 'success'),
                $dataProperty
            ]
        );

        parent::__construct(
            response: $statusCode,
            description: $description,
            content: $content,
            headers: $headers
        );
    }
}