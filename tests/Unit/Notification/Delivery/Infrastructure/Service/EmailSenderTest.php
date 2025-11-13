<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Infrastructure\Service;

use App\Notification\Delivery\Infrastructure\Enum\EmailType;
use App\Notification\Delivery\Infrastructure\Service\EmailSender;
use App\Notification\Delivery\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailSenderTest extends TestCase
{
    private MailerInterface&MockObject $mailerMock;
    private EmailSender $service;

    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->service = new EmailSender($this->mailerMock);
    }

    public function testSupportsReturnsTrueForSupportedCommunicationChannel(): void
    {
        $result = $this->service->supports(CommunicationChannel::EMAIL);
        $this->assertTrue($result);
    }

    public function testSupportsReturnsFalseForUnsupportedCommunicationChannel(): void
    {
        $result = $this->service->supports(CommunicationChannel::SMS);
        $this->assertFalse($result);
    }

    public function testSendSendsCorrectEmailMessage(): void
    {
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationType = NotificationType::INFO;
        $notificationData = ['message' => 'My message'];
        $expectedEmailType = EmailType::fromNotificationType($notificationType);

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($arg) => 
                $arg instanceof TemplatedEmail &&
                $arg->getTo()[0]->getEncodedAddress() == $recipient->getEmail()->getValue() &&
                $arg->getHtmlTemplate() == $expectedEmailType->getTemplatePath() &&
                $arg->getSubject() == $expectedEmailType->getSubject() &&
                $arg->getContext() == $notificationData
            ));

        $this->service->send($recipient, $notificationType, $notificationData);
    }
}