<?php

declare(strict_types=1);

namespace App\Tests\Integration\DataProvider;

use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationType;

class SendNotificationDataProvider
{
    
    public static function validDataCases()
    {
        return [
            [
                [
                    'recipient' => [
                        'identifier' => '596abaec-72b1-47ed-bbf4-d0c951fe9009',
                        'email' => 'user@example.com',
                        'phone' => '+48131352515'
                    ],
                    'type' => NotificationType::INFO->value,
                    'message' => 'Hello',
                    'channels' => [
                        Channel::EMAIL->value
                    ]
                ],
                [
                    'recipient' => [
                        'identifier' => '596abaec-72b1-47ed-bbf4-d0c951fe9009',
                        'email' => 'user@example.com',
                        'phone' => '+48131352515'
                    ],
                    'type' => NotificationType::INFO->value,
                    'message' => 'Hello',
                    'channels' => [
                        Channel::SMS->value
                    ]
                ],
                [
                    'recipient' => [
                        'identifier' => '596abaec-72b1-47ed-bbf4-d0c951fe9009',
                        'email' => 'user@example.com',
                        'phone' => '+48131352515'
                    ],
                    'type' => NotificationType::INFO->value,
                    'message' => 'Hello',
                    'channels' => [
                        Channel::EMAIL->value,
                        Channel::SMS->value
                    ]
                ],
            ]
        ];
    }

    public static function validationDataCases()
    {
        return [
            [
                [
                    'recipient' => [
                        'identifier' => '',
                        'email' => 'user',
                        'phone' => ''
                    ],
                    'type' => 'invalid',
                    'message' => '',
                    'channels' => [

                    ]
                ],
                [
                    'recipient' => [
                        'identifier' => [
                            'This value should not be blank.'
                        ],
                        'email' => [
                            'This value is not a valid email address.'
                        ],
                        'phone' => [
                            'This value should not be blank.'
                        ]
                    ],
                    'type' => [
                        'The value you selected is not a valid channel.'
                    ],
                    'message' => [
                            'This value should not be blank.'
                    ],
                    'channels' => [
                        'You must provide at least 1 channel.'
                    ]
                ]
            ],
            [
                [
                    'recipient' => [
                        'identifier' => '596abaec-72b1-47ed-bbf4-d0c951fe9009',
                        'email' => 'user@example.com',
                        'phone' => '+48131352515'
                    ],
                    'type' => NotificationType::INFO->value,
                    'message' => 'Hello',
                    'channels' => [
                        'invalid'
                    ]
                ],
                [
                    'channels' => [
                        'One or more of the given values is invalid.'
                    ]
                ]
            ],
        ];
    }
}