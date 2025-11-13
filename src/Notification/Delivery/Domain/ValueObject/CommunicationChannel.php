<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Domain\ValueObject;

enum CommunicationChannel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
}