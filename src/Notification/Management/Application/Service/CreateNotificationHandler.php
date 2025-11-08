<?php

declare(strict_types=1);

namespace App\Notification\Management\Application\Service;

use App\Notification\Management\Application\Command\CreateNotificationCommand;
use App\Notification\Management\Domain\Factory\NotificationFactory;
use App\Notification\Management\Domain\Repository\NotificationRepositoryInterface;
use App\Notification\Management\Domain\Service\NotificationManagementPolicyInterface;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateNotificationHandler
{
    /**
     * @param iterable<NotificationManagementPolicyInterface> $notificationPolicies
     */
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private EventDispatcherInterface $eventDispatcher,
        private NotificationFactory $notificationFactory,
    )
    {

    }

    public function __invoke(CreateNotificationCommand $command): void
    {
        $notificationType = NotificationType::from($command->getNotificationType());
        $recipientDto = $command->getRecipient();
        $recipient = new Recipient(
            $recipientDto->getCustomerIdentifier(), 
            new Email($recipientDto->getEmail()),
            new Phone($recipientDto->getPhone())
        );
        $notificationId = new NotificationId(Uuid::uuid4()->toString());

        $notification = $this->notificationFactory->createNotification(
            $notificationId,
            $recipient,
            $notificationType,
            $command->getNotificationData()
        );
        
        $this->notificationRepository->save($notification);
        foreach($notification->pullDomainEvents() as $event){
            $this->eventDispatcher->dispatch($event);
        }
    }
}