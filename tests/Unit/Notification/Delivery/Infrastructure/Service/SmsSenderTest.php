<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Infrastructure\Service;

use App\Notification\Delivery\Infrastructure\Enum\SmsType;
use App\Notification\Delivery\Infrastructure\Service\SmsSender;
use App\Notification\Shared\Domain\ValueObject\CommunicationChannel;
use App\Notification\Shared\Domain\ValueObject\Email;
use App\Notification\Shared\Domain\ValueObject\NotificationType;
use App\Notification\Shared\Domain\ValueObject\Phone;
use App\Notification\Shared\Domain\ValueObject\Recipient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Twig\Environment;

class SmsSenderTest extends TestCase
{
    private TexterInterface&MockObject $texterMock;
    private Environment&MockObject $twigMock;
    private SmsSender $service;

    protected function setUp(): void
    {
        $this->texterMock = $this->createMock(TexterInterface::class);
        $this->twigMock = $this->createMock(Environment::class);
        $this->service = new SmsSender($this->texterMock, $this->twigMock);
    }

    public function testSupportsReturnsTrueForSupportedCommunicationChannel(): void
    {
        $result = $this->service->supports(CommunicationChannel::SMS);
        $this->assertTrue($result);
    }

    public function testSupportsReturnsFalseForUnsupportedCommunicationChannel(): void
    {
        $result = $this->service->supports(CommunicationChannel::EMAIL);
        $this->assertFalse($result);
    }

    public function testSendSendsCorrectSmsMessage(): void
    {
        $email = new Email('user@example.com');
        $phone = new Phone('+48213721372');
        $recipient = new Recipient('2a8045fd', $email, $phone);
        $notificationType = NotificationType::INFO;
        $notificationData = ['message' => 'My message'];
        $expectedSmsType = SmsType::fromNotificationType($notificationType);
        $messageMock = 'message';

        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $expectedSmsType->getTemplatePath(),
                $notificationData
            )
            ->willReturn($messageMock);

        $this->texterMock
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($arg) => 
                $arg instanceof SmsMessage &&
                $arg->getPhone() == $recipient->getPhone()->getValue() &&
                $arg->getSubject() == $messageMock
            ));

        $this->service->send($recipient, $notificationType, $notificationData);
    }
}