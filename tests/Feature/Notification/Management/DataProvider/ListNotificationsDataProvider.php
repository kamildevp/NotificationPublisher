<?php

declare(strict_types=1);

namespace App\Tests\Feature\Notification\Management\DataProvider;

class ListNotificationsDataProvider
{    
    /** @return mixed[] */
    public static function listDataCases(): array
    {
        return [
            [1, 20, 35],
            [2, 20, 35],
            [1, 10, 35],
            [3, 10, 35],
            [1, 10, 1, 'Recipient 1'],
        ];
    }
}