<?php

declare(strict_types=1);

namespace App\DataFixtures\Notification\Management;

use App\Notification\Management\Domain\Entity\Notification;
use App\Notification\Shared\Domain\Entity\ValueObject\NotificationId;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class ListNotificationsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 35; $i++) {
            $notification = Notification::create(
                new NotificationId(Uuid::uuid4()->toString()),
                NotificationType::ALERT,
                ['message' => 'value'],
                new Recipient(
                    'Recipient '.$i,
                    new Email("user$i@example.com"),
                    new Phone('+48213721372')
                )
            );

            $manager->persist($notification);
        }

        $manager->flush();
    }
}
