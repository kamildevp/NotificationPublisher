<?php 

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Common\RecipientDTO;
use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationType;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;


class SendNotificationCommand
{
    #[Assert\Valid]
    private RecipientDTO $recipient;

    #[Assert\Choice(callback: [NotificationType::class, 'values'], message: 'The value you selected is not a valid channel.')]
    private string $type;

    #[Assert\NotBlank]
    private string $message;

    #[OA\Property(
    type: 'array',
    items: new OA\Items(
        type: 'string',
    ),
    minItems: 1
)]
    #[Assert\Choice(
        callback: [Channel::class, 'values'], 
        multiple: true, 
        min: 1,
        minMessage: 'You must provide at least 1 channel.'
    )]
    private array $channels;


    public function __construct(
        RecipientDTO $recipient,
        string $type,
        string $message,
        array $channels,
    )
    {
        $this->recipient = $recipient;
        $this->type = $type;
        $this->message = $message;
        $this->channels = $channels;
    }

    public function getRecipient(): RecipientDTO
    {
        return $this->recipient;   
    }

    public function getType(): string
    {
        return $this->type;   
    }

    public function getMessage(): string
    {
        return $this->message;   
    }

    public function getChannels(): array
    {
        return $this->channels;   
    }
}