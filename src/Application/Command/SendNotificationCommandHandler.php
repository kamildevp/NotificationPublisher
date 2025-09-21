<?php 

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationType;
use App\Domain\Factory\NotificationFactory;
use App\Domain\ValueObject\Recipient;
use App\Infrastructure\Repository\NotificationRepository;
use DomainException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class SendNotificationCommandHandler
{
    public function __construct(
        private NotificationFactory $notificationFactory,
        private NotificationRepository $notificationRepository,
        private MessageBusInterface $eventBus,
    )
    {
        
    }

    public function __invoke(SendNotificationCommand $command)
    {
        foreach($command->getChannels() as $channel)
        {
            try{
                $recipientDTO = $command->getRecipient();
                $recipient = new Recipient($recipientDTO->getIdentifier(), $recipientDTO->getEmail(), $recipientDTO->getPhone());
                $recipientRecentNotificationsCount = $this->notificationRepository->getRecentNotificationCountForRecipient(
                    $recipient->getIdentifier(),
                    $command->getType()
                );

                $notification = $this->notificationFactory->create(
                    NotificationType::from($command->getType()),
                    $command->getMessage(),
                    $recipient,
                    Channel::from($channel),
                    $recipientRecentNotificationsCount
                );
            }
            catch(DomainException){
                continue;
            }

            $this->notificationRepository->save($notification);

            foreach ($notification->popEvents() as $event) {
                $this->eventBus->dispatch($event);
            }
        }
    }
}