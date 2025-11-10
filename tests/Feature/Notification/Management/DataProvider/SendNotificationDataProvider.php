<?php

declare(strict_types=1);

namespace App\Tests\Feature\Notification\Management\DataProvider;

use App\Notification\Management\UI\Enum\NotificationType;

class SendNotificationDataProvider
{
    /** @return mixed[] */
    public static function validDataCases(): array
    {
        return [
            [
                [
                    'recipient' => [
                        'customer_identifier' => '596abaec-72b1-47ed-bbf4-d0c951fe9009',
                        'email' => 'user@example.com',
                        'phone' => '+48131352515'
                    ],
                    'notification_type' => NotificationType::INFO->value,
                    'notification_data' => [
                        'message' => 'Hello',
                    ]
                ],
            ],
            [
                [
                    'recipient' => [
                        'customer_identifier' => '596abaec-72b1-47ed-bbf4-d0c951fe9009',
                        'email' => 'user@example.com',
                        'phone' => '+48131352515'
                    ],
                    'notification_type' => NotificationType::ALERT->value,
                    'notification_data' => [
                        'message' => 'Hello',
                    ]
                ],
            ]
        ];
    }

    /** @return mixed[] */
    public static function validationDataCases(): array
    {
        return [
            [
                [
                    'recipient' => [
                        'customer_identifier' => '',
                        'email' => '',
                        'phone' => ''
                    ],
                    'notification_type' => 'invalid',
                    'notification_data' => [
                        'message' => '',
                    ]
                ],
                [
                    'recipient' => [
                        'customer_identifier' => [
                            'This value should not be blank.'
                        ],
                        'email' => [
                            'This value should not be blank.'
                        ],
                        'phone' => [
                            'This value should not be blank.'
                        ]
                    ],
                    'notification_type' => [
                        'The value you selected is not a valid choice.'
                    ],
                ]
            ],
            [
                [
                    'recipient' => [
                        'customer_identifier' => str_repeat('a', 256),
                        'email' => 'user',
                        'phone' => 'a'
                    ],
                    'notification_type' => NotificationType::INFO,
                    'notification_data' => [
                        'message' => '',
                    ]
                ],
                [
                    'recipient' => [
                        'customer_identifier' => [
                            'This value is too long. It should have 255 characters or less.'
                        ],
                        'email' => [
                            'This value is not a valid email address.'
                        ],
                        'phone' => [
                            'This value is not a valid phone number.'
                        ]
                    ],
                    'notification_data' => [
                        'message' => [
                            'This value should not be blank.'
                        ]
                    ],
                ]
            ],
                        [
                [
                    'recipient' => [
                        'customer_identifier' => str_repeat('a', 256),
                        'email' => 'user',
                        'phone' => 'a'
                    ],
                    'notification_type' => NotificationType::ALERT,
                    'notification_data' => [
                        'message' => '',
                    ]
                ],
                [
                    'recipient' => [
                        'customer_identifier' => [
                            'This value is too long. It should have 255 characters or less.'
                        ],
                        'email' => [
                            'This value is not a valid email address.'
                        ],
                        'phone' => [
                            'This value is not a valid phone number.'
                        ]
                    ],
                    'notification_data' => [
                        'message' => [
                            'This value should not be blank.'
                        ]
                    ],
                ]
            ],
        ];
    }
}