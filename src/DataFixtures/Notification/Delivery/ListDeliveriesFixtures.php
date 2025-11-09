<?php

declare(strict_types=1);

namespace App\DataFixtures\Notification\Delivery;

use App\Notification\Delivery\Domain\Entity\Delivery;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\Entity\ValueObject\DeliveryId;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class ListDeliveriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 35; $i++) {
            $delivery = Delivery::schedule(
                new DeliveryId(Uuid::uuid4()->toString()),
                new NotificationId(Uuid::uuid4()->toString()),
                NotificationType::ALERT,
                CommunicationChannel::EMAIL,
                ['message' => 'value'],
                new Recipient(
                    'Recipient '.$i,
                    new Email("user$i@example.com"),
                    new Phone('+48213721372')
                )
            );

            $manager->persist($delivery);
        }

        $manager->flush();
    }
}
