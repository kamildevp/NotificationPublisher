<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Doctrine\Type;

use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Shared\Infrastructure\Doctrine\Type\BackedStringEnumType;

final class CommunicationChannelType extends BackedStringEnumType
{
    public function getName(): string
    {
        return 'communication_channel';
    }

    protected function typeClassName(): string
    {
        return CommunicationChannel::class;
    }
}
