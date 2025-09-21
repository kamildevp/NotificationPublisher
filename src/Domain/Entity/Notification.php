<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Enum\NotificationType;
use App\Infrastructure\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    use RecordsEventsTrait;

    #[ORM\Id]
    #[ORM\Column]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $channel = null;

    #[ORM\Column(length: 255)]
    private ?string $recipientIdentifier = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getType(): ?NotificationType
    {
        return $this->type ? NotificationType::from($this->type) : null;
    }

    public function setType(NotificationType $type): static
    {
        $this->type = $type->value;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel ? Channel::from($this->channel) : null;
    }

    public function setChannel(Channel $channel): static
    {
        $this->channel = $channel->value;

        return $this;
    }

    public function getRecipientIdentifier(): ?string
    {
        return $this->recipientIdentifier;
    }

    public function setRecipientIdentifier(string $recipientIdentifier): static
    {
        $this->recipientIdentifier = $recipientIdentifier;

        return $this;
    }

    public function getStatus(): ?NotificationStatus
    {
        return $this->status ? NotificationStatus::from($this->status) : null;
    }

    public function setStatus(NotificationStatus $status): static
    {
        $this->status = $status->value;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }
    

    public function setSentAt(\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }
}
