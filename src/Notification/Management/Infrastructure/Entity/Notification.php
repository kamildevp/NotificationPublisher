<?php

declare(strict_types=1);

namespace App\Notification\Management\Infrastructure\Entity;

use App\Notification\Management\Infrastructure\Repository\NotificationRepository;
use App\Notification\Shared\Infrastructure\Entity\Embeddable\Recipient;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Embedded(class: Recipient::class)]
    private Recipient $recipient;

    #[ORM\Column]
    private array $data = [];

    #[ORM\Column(length: 255)]
    private string $state;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    public function setRecipient(Recipient $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
