<?php

declare(strict_types=1);

namespace Tests\Application\Service;

use App\Application\Service\NotificationChannelServiceInterface;
use App\Application\Service\NotificationChannelServiceResolver;
use App\Domain\Enum\Channel;
use Exception;
use PHPUnit\Framework\TestCase;

class NotificationChannelServiceResolverTest extends TestCase
{
    public function testResolveReturnsCorrectService(): void
    {
        $emailServiceMock = $this->createMock(NotificationChannelServiceInterface::class);
        $smsServiceMock = $this->createMock(NotificationChannelServiceInterface::class);

        $emailServiceMock->method('supports')->willReturnCallback(fn($channel) => $channel === Channel::EMAIL);
        $smsServiceMock->method('supports')->willReturnCallback(fn($channel) => $channel === Channel::SMS);

        $resolver = new NotificationChannelServiceResolver([$emailServiceMock, $smsServiceMock]);

        $resolvedEmailService = $resolver->resolve(Channel::EMAIL);
        $resolvedSmsService = $resolver->resolve(Channel::SMS);

        $this->assertSame($emailServiceMock, $resolvedEmailService);
        $this->assertSame($smsServiceMock, $resolvedSmsService);
    }

    public function testResolveThrowsExceptionWhenNoServiceSupportsChannel(): void
    {
        $serviceMock = $this->createMock(NotificationChannelServiceInterface::class);
        $serviceMock->method('supports')->willReturn(false);

        $resolver = new NotificationChannelServiceResolver([$serviceMock]);

        $this->expectException(Exception::class);

        $resolver->resolve(Channel::EMAIL);
    }
}
