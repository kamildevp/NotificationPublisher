<?php 

declare(strict_types=1);

namespace App\Notification\Delivery\Application\EventSubscriber;

use App\Notification\Delivery\Application\Command\DeliverNotificationCommand;
use App\Notification\Delivery\Domain\Event\DeliveryScheduledEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeliveryScheduledSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    )
    {

    }

    public function onDeliveryScheduled(DeliveryScheduledEvent $event): void
    {
        $this->commandBus->dispatch(new DeliverNotificationCommand(
            $event->getDeliveryId(),
            $event->getNotificationId(),
            $event->getNotificationType(),
            $event->getCommunicationChannel(),
            $event->getNotificationRecipient(),
            $event->getNotificationData(),
        ));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DeliveryScheduledEvent::class => 'onDeliveryScheduled',
        ];
    }
}
