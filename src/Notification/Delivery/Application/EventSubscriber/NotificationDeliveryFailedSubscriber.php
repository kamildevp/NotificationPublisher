<?php 

declare(strict_types=1);

namespace App\Notification\Delivery\Application\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use App\Notification\Delivery\Application\Command\DeliverNotificationCommand;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Delivery\Domain\Service\DeliveryRetryPolicy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class NotificationDeliveryFailedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private EventDispatcherInterface $eventDispatcher,
        private DeliveryRetryPolicy $deliveryRetryPolicy,
        private DeliveryRepositoryInterface $deliveryRepository,
    ) 
    {

    }

    public function onNotificationDeliveryFailed(WorkerMessageFailedEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $message = $envelope->getMessage();

        if (!$message instanceof DeliverNotificationCommand || $event->willRetry()) {
            return;
        }

        $attemptNr = $message->getAttemptNr();
        if($this->deliveryRetryPolicy->shouldRetry($attemptNr)){
            $delay = $this->deliveryRetryPolicy->getRetryDelay($attemptNr);
            $attemptNr++;

            $this->commandBus->dispatch(new DeliverNotificationCommand(
                $message->getDeliveryId(),
                $message->getNotificationType(),
                $message->getCommunicationChannel(),
                $message->getNotificationRecipient(),
                $message->getNotificationData(),
                $attemptNr
            ), [new DelayStamp($delay)]);
            return;
        }

        $delivery = $this->deliveryRepository->findById($message->getDeliveryId());
        if(!$delivery){
            return;
        }
        
        $delivery->markFailed();
        $this->deliveryRepository->save($delivery);
        foreach($delivery->pullDomainEvents() as $event){
            $this->eventDispatcher->dispatch($event);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onNotificationDeliveryFailed',
        ];
    }
}
