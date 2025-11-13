<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Documentation\Content;

use App\Notification\Management\UI\DTO\SendNotificationDTO;
use App\Notification\Management\UI\Enum\NotificationType;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class SendNotificationDTOContentDoc extends OA\JsonContent
{
    public function __construct()
    {
        $baseFieldsExamples = [
            'recipient' => [
                'customer_identifier' => '596abaec-72b1-47ed-bbf4-d0c951fe9009',
                'email' => 'user@example.com',
                'phone' => '+48213721372'
            ],
        ];

        $examples = [];
        foreach(NotificationType::cases() as $notificationType){
            $examples[$notificationType->value] = new OA\Examples(
                summary: ucfirst($notificationType->value),
                example: ucfirst($notificationType->value), 
                value: [
                    ...$baseFieldsExamples,
                    'notification_type' => $notificationType->value,
                    'notification_data' => $notificationType->getDataExample()
                ]
            );
        }

        parent::__construct(
            ref: new Model(type: SendNotificationDTO::class),
            examples: $examples
        );
    }
}