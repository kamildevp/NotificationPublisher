<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Application\Service;

use App\Notification\Delivery\Application\Command\DeliverNotificationCommand;
use App\Notification\Delivery\Domain\Repository\DeliveryRepositoryInterface;
use App\Notification\Delivery\Infrastructure\Service\CommunicationChannelSenderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeliverNotificationHandler
{
    /**
     * @param iterable<CommunicationChannelSenderInterface> $communicationChannelSenders
     */
    public function __construct(
        #[AutowireIterator('communication_channel_sender')]
        private iterable $communicationChannelSenders,
        private DeliveryRepositoryInterface $deliveryRepository,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
        
    }

    public function __invoke(DeliverNotificationCommand $command): void
    {
        $matchingSender = null;
        foreach($this->communicationChannelSenders as $sender){
            if($sender->supports($command->getCommunicationChannel())){
                $matchingSender = $sender;
                break;
            }
        }

        if(!$matchingSender){
            return;
        }

        $matchingSender->send(
            $command->getNotificationRecipient(), 
            $command->getNotificationType(), 
            $command->getNotificationData()
        );

        $delivery = $this->deliveryRepository->findById($command->getDeliveryId());
        if(!$delivery){
            return;
        }

        $delivery->markCompleted();
        $this->deliveryRepository->save($delivery);
        foreach($delivery->pullDomainEvents() as $event){
            $this->eventDispatcher->dispatch($event);
        }
    }
}