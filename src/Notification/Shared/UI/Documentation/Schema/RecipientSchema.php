<?php

declare(strict_types=1);

namespace App\Notification\Shared\UI\Documentation\Schema;

use OpenApi\Attributes as OA;

class RecipientSchema extends OA\Schema
{
    public function __construct()
    {
        $properties = [
            new OA\Property(
                property: 'customer_identifier', 
                type: 'string', 
                example: '596abaec-72b1-47ed-bbf4-d0c951fe9009'
            ),
            new OA\Property(
                property: 'email', 
                type: 'string', 
                format: 'email',
                example: 'user@example.com',
            ),
            new OA\Property(
                property: 'phone', 
                type: 'string', 
                example: '+48213721372',
            ),
        ];

        parent::__construct(type: 'object', properties: $properties);
    }
}