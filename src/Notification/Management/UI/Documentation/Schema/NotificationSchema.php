<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Documentation\Schema;

use App\Notification\Management\UI\Enum\NotificationType;
use App\Notification\Shared\UI\Documentation\Schema\RecipientSchema;
use OpenApi\Attributes as OA;

class NotificationSchema extends OA\Schema
{
    public function __construct()
    {
        $properties = [
            new OA\Property(
                property: 'id', 
                type: 'string', 
                example: '596abaec-72b1-47ed-bbf4-d0c951fe9009'
            ),
            new OA\Property(
                property: 'type', 
                type: 'string', 
                example: NotificationType::ALERT->value
            ),
            new OA\Property(
                property: 'data', 
                type: 'object', 
                example: NotificationType::ALERT->getDataExample()
            ),
            new OA\Property(
                property: 'recipient', 
                type: 'object', 
                properties: (new RecipientSchema())->properties
            ),
            new OA\Property(
                property: 'state', 
                type: 'string', 
                example: 'scheduled'
            ),
            new OA\Property(
                property: 'created_at', 
                type: 'string', 
                format: 'date-time'
            ),
        ];

        parent::__construct(type: 'object', properties: $properties);
    }
}