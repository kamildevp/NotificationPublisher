<?php

declare(strict_types=1);

namespace App\UserInterface\Documentation\Response;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class SuccessResponseDoc extends OA\Response
{
    public function __construct(
        int $statusCode = 200, 
        ?string $description = 'Success Response', 
        ?string $dataModel = null, 
        ?OA\Property $dataProperty = null,
        mixed $dataExample = Generator::UNDEFINED,
        array $headers = []
    )
    {
        if(!$dataProperty){
            $dataProperty = !is_null($dataModel) ? 
                new OA\Property(property: 'data', ref: new Model(type: $dataModel)) : 
                new OA\Property(property: 'data', type: 'object', example: $dataExample);
        }


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