<?php 

declare(strict_types=1);

namespace App\Notification\Delivery\Application\EventSubscriber;

use App\Notification\Delivery\Domain\Factory\DeliveryFactory;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private DeliveryRepositoryInterface $deliveryRepository,
        private EventDispatcherInterface $eventDispatcher,
        private DeliveryFactory $deliveryFactory,
    ) 
    {

    }

    public function onNotificationCreated(NotificationCreatedEvent $event): void
    {
        $deliveries = $this->deliveryFactory->scheduleNotificationDeliveries(
            $event->getNotificationId(),
            $event->getNotificationRecipient(),
            $event->getNotificationType(),
            $event->getNotificationData()
        );

        foreach($deliveries as $delivery){
            $this->deliveryRepository->save($delivery);
            foreach($delivery->pullDomainEvents() as $event){
                $this->eventDispatcher->dispatch($event);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NotificationCreatedEvent::class => 'onNotificationCreated',
        ];
    }
}
