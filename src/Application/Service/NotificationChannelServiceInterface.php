<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Enum\Channel;
use App\Domain\Event\NotificationCreatedEvent;

interface NotificationChannelServiceInterface
{
    public function supports(Channel $channel): bool;

    public function send(NotificationCreatedEvent $event): void;
}