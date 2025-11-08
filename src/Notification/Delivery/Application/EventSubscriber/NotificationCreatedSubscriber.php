<?php 

declare(strict_types=1);

namespace App\Notification\Delivery\Application\EventSubscriber;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Management\Domain\Event\NotificationCreatedEvent;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable<NotificationDeliveryPolicyInterface> $notificationDeliveryPolices
     */
    public function __construct(
        private DeliveryRepositoryInterface $deliveryRepository,
        private EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator('notification_delivery_policy_service')]
        private iterable $notificationDeliveryPolices,
    ) 
    {

    }

    public function onNotificationCreated(NotificationCreatedEvent $event): void
    {
        foreach(CommunicationChannel::cases() as $communicationChannel)
        {
            $policiesPassed = true;
            foreach($this->notificationDeliveryPolices as $notificationDeliveryPolicy)
            {
                if(!$notificationDeliveryPolicy->canNotificationBeDelivered(
                    $event->getNotificationRecipient(), 
                    $event->getNotificationType(), 
                    $event->getNotificationData(), 
                    $communicationChannel
                )){
                    $policiesPassed = false;
                    break;
                }
            }
            
            if(!$policiesPassed){
                continue;
            }

            $deliveryId = new DeliveryId(Uuid::uuid4()->toString());
            $delivery = Delivery::schedule(
                $deliveryId,
                $event->getNotificationId(),
                $event->getNotificationType(),
                $communicationChannel,
                $event->getNotificationData(),
                $event->getNotificationRecipient()
            );

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
