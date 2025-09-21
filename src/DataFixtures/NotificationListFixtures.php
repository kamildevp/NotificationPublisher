<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Notification;
use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationStatus;
use App\Domain\Enum\NotificationType;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NotificationListFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $notificationTypes = NotificationType::cases();
        $channels = Channel::cases();
        $notificationStatuses = NotificationStatus::cases();

        for ($i = 1; $i <= 35; $i++) {
            $notification = new Notification();
            $notification->setType($notificationTypes[array_rand($notificationTypes)]);
            $notification->setMessage('Hello '.$i);
            $notification->setRecipientIdentifier('Recipient '.$i);
            $notification->setChannel($channels[array_rand($channels)]);
            $notification->setStatus($notificationStatuses[array_rand($notificationStatuses)]);
            $notification->setCreatedAt(new DateTimeImmutable());

            $manager->persist($notification);
        }

        $manager->flush();
    }
}
