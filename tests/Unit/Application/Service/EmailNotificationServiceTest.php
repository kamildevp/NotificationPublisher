<?php

declare(strict_types=1);

namespace Tests\Application\Service;

use App\Application\Service\EmailNotificationService;
use App\Domain\Enum\Channel;
use App\Domain\Enum\NotificationType;
use App\Domain\Event\NotificationCreatedEvent;
use App\Domain\ValueObject\Recipient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailNotificationServiceTest extends TestCase
{
    private MailerInterface&MockObject $mailerMock;
    private EmailNotificationService $service;

    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->service = new EmailNotificationService($this->mailerMock);
    }

    public function testSupportsEmailChannel(): void
    {
        $this->assertTrue($this->service->supports(Channel::EMAIL));
        $this->assertFalse($this->service->supports(Channel::SMS));
    }

    public function testSendCallsMailerWithCorrectEmail(): void
    {
        $recipientEmail = 'user@example.com';
        $recipient = $this->createMock(Recipient::class);
        $recipient->method('getEmail')->willReturn($recipientEmail);

        $notificationType = NotificationType::INFO;
        $emailType = $notificationType->getEmailType();
        $subject = $emailType->getSubject();
        $templatePath = $emailType->getTemplatePath();
        $message = 'Hello World';

        $eventMock = $this->createMock(NotificationCreatedEvent::class);
        $eventMock->method('getRecipient')->willReturn($recipient);
        $eventMock->method('getMessage')->willReturn('Hello World');
        $eventMock->method('getNotificationType')->willReturn($notificationType);

        $this->mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (TemplatedEmail $email) use ($subject, $templatePath, $message, $recipientEmail) {
                return $email->getTo()[0]->getAddress() === $recipientEmail
                    && $email->getSubject() === $subject
                    && $email->getHtmlTemplate() === $templatePath
                    && $email->getContext()['message'] === $message;
            }));

        $this->service->send($eventMock);
    }
}
