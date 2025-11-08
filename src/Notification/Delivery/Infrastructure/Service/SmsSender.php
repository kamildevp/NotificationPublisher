<?php

declare(strict_types=1);

namespace App\Notification\Delivery\Infrastructure\Service;

use App\Notification\Delivery\Infrastructure\Enum\SmsType;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Twig\Environment;

class SmsSender implements CommunicationChannelSenderInterface
{
    public function __construct(private TexterInterface $texter, private Environment $twig)
    {
        
    }

    public function supports(CommunicationChannel $communicationChannel): bool
    {
        return $communicationChannel == CommunicationChannel::SMS;
    }

    public function send(Recipient $recipient, NotificationType $notificationType, array $notificationData): void
    {
        $smsType = SmsType::fromNotificationType($notificationType);
        $message = $this->twig->render($smsType->getTemplatePath(), $notificationData);
        $smsMessage = new SmsMessage($recipient->getPhone()->getValue(), $message);
        
        $this->texter->send($smsMessage);
    }
}