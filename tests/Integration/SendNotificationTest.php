<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Application\Command\SendNotificationCommand;
use App\Domain\Enum\NotificationStatus;
use App\Infrastructure\Repository\NotificationRepository;
use App\Tests\Integration\DataProvider\SendNotificationDataProvider;
use App\Tests\Utils\BaseWebTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class SendNotificationTest extends BaseWebTestCase
{
    protected InMemoryTransport $messengerTransport;
    protected NotificationRepository $notificationRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messengerTransport = $this->container->get('messenger.transport.async');
        $this->notificationRepository = $this->container->get(NotificationRepository::class);
    }

    #[DataProviderExternal(SendNotificationDataProvider::class, 'validDataCases')]
    public function testNotificationFlow(array $params): void
    {
        $responseData = $this->getSuccessfulResponseData($this->client,'POST', '/api/notification', $params);
        $this->assertEquals(['notification_status' => NotificationStatus::PENDING->value], $responseData);
        
        $envelopes = $this->messengerTransport->getSent();
        $this->assertCount(1, $envelopes);
        $command = $envelopes[0]->getMessage();

        $this->assertInstanceOf(SendNotificationCommand::class, $command);
        $this->assertEquals($params['recipient']['identifier'], $command->getRecipient()->getIdentifier());
        $this->assertEquals($params['recipient']['email'], $command->getRecipient()->getEmail());
        $this->assertEquals($params['recipient']['phone'], $command->getRecipient()->getPhone());
        $this->assertEquals($params['type'], $command->getType());
        $this->assertEquals($params['message'], $command->getMessage());
        $this->assertEquals($params['channels'], $command->getChannels());
    }

    #[DataProviderExternal(SendNotificationDataProvider::class, 'validationDataCases')]
    public function testCreateValidation(array $params, array $expectedErrors): void
    {
        $this->assertPathValidation($this->client, 'POST', '/api/notification', $params, $expectedErrors);
        $this->assertCount(0, $this->messengerTransport->getSent());
    }
}
