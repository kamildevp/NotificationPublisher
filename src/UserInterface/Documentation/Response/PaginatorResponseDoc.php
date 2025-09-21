<?php

declare(strict_types=1);

namespace App\UserInterface\Documentation\Response;

use App\Application\Common\PaginationResult;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PaginatorResponseDoc extends SuccessResponseDoc
{
    public function __construct(
        string $dataModel, 
        string $description = 'Pagination Response', 
        array $headers = []
    )
    {
        $dataProperty = new OA\Property(property: 'data', allOf: [
            new OA\Schema(ref: new Model(type: PaginationResult::class)),
            new OA\Schema(type: 'object', properties: [
                new OA\Property(
                    property: 'items',      
                    type: 'array',     
                    items: new OA\Items(ref: new Model(type: $dataModel))
                )
            ])
        ]); 

        parent::__construct(
            description: $description,
            dataProperty: $dataProperty,
            headers: $headers
        );
    }
}