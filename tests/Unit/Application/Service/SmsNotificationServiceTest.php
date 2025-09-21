<?php

declare(strict_types=1);

namespace Tests\Application\Service;

use App\Application\Service\SmsNotificationService;
use App\Domain\Enum\Channel;
use App\Domain\Event\NotificationCreatedEvent;
use App\Domain\ValueObject\Recipient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

class SmsNotificationServiceTest extends TestCase
{
    private TexterInterface&MockObject $texterMock;
    private SmsNotificationService $service;

    protected function setUp(): void
    {
        $this->texterMock = $this->createMock(TexterInterface::class);
        $this->service = new SmsNotificationService($this->texterMock);
    }

    public function testSupportsSmsChannel(): void
    {
        $this->assertTrue($this->service->supports(Channel::SMS));
        $this->assertFalse($this->service->supports(Channel::EMAIL));
    }

    public function testSendCallsTexterWithCorrectSmsMessage(): void
    {
        $recipientPhone = '123456789';
        $recipientMock = $this->createMock(Recipient::class);
        $recipientMock->method('getPhone')->willReturn($recipientPhone);

        $message = 'Hello';
        $eventMock = $this->createMock(NotificationCreatedEvent::class);
        $eventMock->method('getRecipient')->willReturn($recipientMock);
        $eventMock->method('getMessage')->willReturn($message);

        $this->texterMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (SmsMessage $sms) use ($message, $recipientPhone) {
                return $sms->getSubject() === $message
                    && $sms->getPhone() === $recipientPhone;
            }));

        $this->service->send($eventMock);
    }
}
