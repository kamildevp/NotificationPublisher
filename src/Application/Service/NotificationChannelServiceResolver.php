<?php

namespace App\Application\Service;

use App\Domain\Enum\Channel;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class NotificationChannelServiceResolver
{
    public function __construct(#[TaggedIterator('notification_channel_service')] private iterable $services)
    {
        $this->services = $services;
    }

    public function resolve(Channel $channel): NotificationChannelServiceInterface
    {
        foreach ($this->services as $service) {
            if ($service->supports($channel)) {
                return $service;
            }
        }

        throw new Exception("No notification service found for channel {$channel->value}");
    }
}
