<?php 

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use App\Domain\Entity\Notification;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use App\Infrastructure\Repository\NotificationRepository;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Event\NotificationCreatedEvent;

class FailedNotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(private NotificationRepository $notificationRepository) {}

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $message = $envelope->getMessage();

        if (!$message instanceof NotificationCreatedEvent) {
            return;
        }

        $notification = $this->notificationRepository->find($message->getNotificationId());
        if (!$notification instanceof Notification) return;

        $notification->setStatus(NotificationStatus::FAILED);
        $this->notificationRepository->save($notification);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onMessageFailed',
        ];
    }
}
