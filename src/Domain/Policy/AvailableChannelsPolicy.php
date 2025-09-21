<?php

declare(strict_types=1);

namespace App\Domain\Policy;

use App\Domain\Enum\Channel;

class AvailableChannelsPolicy
{
    private array $availableChannels;

    public function __construct(bool $enableEmail, bool $enableSms)
    {
        $this->availableChannels = array_filter([
            $enableEmail ? Channel::EMAIL : null,
            $enableSms   ? Channel::SMS   : null,
        ]);
    }

    public function isAvailable(Channel $channel): bool
    {
        return in_array($channel, $this->availableChannels);
    }
}