<?php

declare(strict_types=1);

namespace App\Tests\Feature\Notification\Delivery\DataProvider;

class ListDeliveriesDataProvider
{    
    public static function listDataCases()
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