<?php

declare(strict_types=1);

namespace App\Notification\Delivery\UI\Documentation\Schema;

use App\Notification\Shared\UI\Documentation\Schema\RecipientSchema;
use OpenApi\Attributes as OA;

class DeliveryListItems extends OA\Items
{
    public function __construct()
    {
        /** @var \OpenApi\Attributes\Property[] */
        $recipientProperties = (new RecipientSchema())->properties;
        $properties = [
            new OA\Property(
                property: 'id', 
                type: 'string', 
                example: '596abaec-72b1-47ed-bbf4-d0c951fe9009'
            ),
            new OA\Property(
                property: 'notification_id', 
                type: 'string', 
                example: '596abaec-72b1-47ed-bbf4-d0c951fe9009'
            ),
            new OA\Property(
                property: 'notification_type', 
                type: 'string', 
                example: 'alert'
            ),
            new OA\Property(
                property: 'communication_channel', 
                type: 'string', 
                example: 'email'
            ),
            new OA\Property(
                property: 'data', 
                type: 'object', 
                example: ['message' => 'My message']
            ),
            new OA\Property(
                property: 'recipient', 
                type: 'object', 
                properties: $recipientProperties
            ),
            new OA\Property(
                property: 'status', 
                type: 'string', 
                example: 'completed'
            ),
            new OA\Property(
                property: 'scheduled_at', 
                type: 'string', 
                format: 'date-time'
            ),
            new OA\Property(
                property: 'completed_at', 
                type: 'string', 
                format: 'date-time'
            ),
        ];

        parent::__construct(type: 'object', properties: $properties);
    }
}