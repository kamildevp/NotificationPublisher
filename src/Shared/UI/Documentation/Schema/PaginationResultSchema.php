<?php

declare(strict_types=1);

namespace App\Shared\UI\Documentation\Schema;

use OpenApi\Attributes as OA;

class PaginationResultSchema extends OA\Schema
{
    public function __construct(
        OA\Schema $itemsSchema
    )
    {
        $properties = [
            new OA\Property(
                property: 'items', 
                type: 'array', 
                items: new OA\Items(type: $itemsSchema->type, properties: $itemsSchema->properties)
            ),
            new OA\Property(
                property: 'page', 
                type: 'integer', 
                example: 1,
            ),
            new OA\Property(
                property: 'per_page', 
                type: 'integer', 
                example: 20,
            ),
            new OA\Property(
                property: 'pages_count', 
                type: 'integer', 
                example: 5,
            ),
            new OA\Property(
                property: 'total', 
                type: 'integer', 
                example: 90,
            ),
        ];

        parent::__construct(type: 'object', properties: $properties);
    }
}