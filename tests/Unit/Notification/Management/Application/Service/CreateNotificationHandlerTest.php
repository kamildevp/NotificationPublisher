<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Management\Application\Service;

use App\Notification\Management\Application\Command\CreateNotificationCommand;
use App\Notification\Management\Application\DTO\RecipientDTO;
use App\Notification\Management\Application\Service\CreateNotificationHandler;
use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use App\Notification\Management\Domain\Event\NotificationDiscardedEvent;
use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use App\Notification\Management\Domain\Service\NotificationManagementPolicyInterface;
use App\Notification\Management\Domain\ValueObject\NotificationState;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use ArrayIterator;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateNotificationHandlerTest extends TestCase
{
    private NotificationRepositoryInterface&MockObject $notificationRepositoryMock;
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private NotificationManagementPolicyInterface&MockObject $notificationManagementPolicyMock;
    private iterable $notificationManagementPoliciesMock;
    private CreateNotificationHandler $service;

    protected function setUp(): void
    {
        $this->notificationRepositoryMock = $this->createMock(NotificationRepositoryInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->notificationManagementPolicyMock = $this->createMock(NotificationManagementPolicyInterface::class);
        $this->notificationManagementPoliciesMock = new ArrayIterator([$this->notificationManagementPolicyMock]);
        $this->service = new CreateNotificationHandler(
            $this->notificationRepositoryMock, 
            $this->eventDispatcherMock,
            $this->notificationManagementPoliciesMock,
        );
    }

    public function testNotificationIsDiscardedWhenPoliciesPass(): void
    {
        $recipientDto = new RecipientDTO('2a8045fd', 'user@example.com', '+48213721372');
        $command = new CreateNotificationCommand(
            $recipientDto,
            NotificationType::ALERT->value,
            ['message' => 'test']
        );

        $this->notificationManagementPolicyMock
            ->method('canNotificationBeSent')
            ->willReturn(true);

        $this->notificationRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn($arg) =>
                $arg instanceof Notification &&
                $arg->getId() instanceof NotificationId &&
                $arg->getRecipient()->getCustomerIdentifier() == $recipientDto->getCustomerIdentifier() &&
                $arg->getRecipient()->getEmail()->getValue() == $recipientDto->getEmail() &&
                $arg->getRecipient()->getPhone()->getValue() == $recipientDto->getPhone() &&
                $arg->getType()->value == $command->getNotificationType() &&
                $arg->getData() == $command->getNotificationData() &&
                $arg->getState() == NotificationState::SCHEDULED && 
                $arg->getCreatedAt() instanceof DateTimeImmutable
            ));

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn($arg) =>
                $arg instanceof NotificationCreatedEvent &&
                $arg->getNotificationId() instanceof NotificationId &&
                $arg->getNotificationType()->value == $command->getNotificationType() &&
                $arg->getNotificationRecipient()->getCustomerIdentifier() == $recipientDto->getCustomerIdentifier() &&
                $arg->getNotificationRecipient()->getEmail()->getValue() == $recipientDto->getEmail() &&
                $arg->getNotificationRecipient()->getPhone()->getValue() == $recipientDto->getPhone() &&
                $arg->getNotificationData() == $command->getNotificationData()
            ));

        ($this->service)($command);
    }

    public function testNotificationIsCreatedWhenPoliciesFail(): void
    {
        $recipientDto = new RecipientDTO('2a8045fd', 'user@example.com', '+48213721372');
        $command = new CreateNotificationCommand(
            $recipientDto,
            NotificationType::ALERT->value,
            ['message' => 'test']
        );

        $this->notificationManagementPolicyMock
            ->method('canNotificationBeSent')
            ->willReturn(false);

        $this->notificationRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(fn($arg) =>
                $arg instanceof Notification &&
                $arg->getId() instanceof NotificationId &&
                $arg->getRecipient()->getCustomerIdentifier() == $recipientDto->getCustomerIdentifier() &&
                $arg->getRecipient()->getEmail()->getValue() == $recipientDto->getEmail() &&
                $arg->getRecipient()->getPhone()->getValue() == $recipientDto->getPhone() &&
                $arg->getType()->value == $command->getNotificationType() &&
                $arg->getData() == $command->getNotificationData() &&
                $arg->getState() == NotificationState::DISCARDED && 
                $arg->getCreatedAt() instanceof DateTimeImmutable
            ));

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn($arg) =>
                $arg instanceof NotificationDiscardedEvent &&
                $arg->getNotificationId() instanceof NotificationId &&
                $arg->getNotificationType()->value == $command->getNotificationType() &&
                $arg->getNotificationRecipient()->getCustomerIdentifier() == $recipientDto->getCustomerIdentifier() &&
                $arg->getNotificationRecipient()->getEmail()->getValue() == $recipientDto->getEmail() &&
                $arg->getNotificationRecipient()->getPhone()->getValue() == $recipientDto->getPhone() &&
                $arg->getNotificationData() == $command->getNotificationData()
            ));

        ($this->service)($command);
    }
}