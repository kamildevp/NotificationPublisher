<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Application\Service\NotificationChannelServiceResolver;
use App\Domain\Entity\Notification;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Event\NotificationCreatedEvent;
use App\Infrastructure\Repository\NotificationRepository;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationCreatedHandler
{
    public function __construct(
        private NotificationChannelServiceResolver $resolver,
        private NotificationRepository $notificationRepository,    
    ) {}

    public function __invoke(NotificationCreatedEvent $event): void
    {
        $service = $this->resolver->resolve($event->getChannel());
        $service->send($event);

        $notification = $this->notificationRepository->find($event->getNotificationId());
        if(!$notification instanceof Notification){
            return;
        }

        $notification->setStatus(NotificationStatus::SENT);
        $notification->setSentAt(new DateTimeImmutable());
        $this->notificationRepository->save($notification);
    }
}
