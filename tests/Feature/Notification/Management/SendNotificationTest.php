<?php

declare(strict_types=1);

namespace App\Tests\Feature\Notification\Management;

use App\Notification\Management\Application\Command\SendNotificationCommand;
use App\Tests\Feature\Notification\Management\DataProvider\SendNotificationDataProvider;
use App\Tests\Utils\BaseWebTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class SendNotificationTest extends BaseWebTestCase
{
    protected InMemoryTransport $messengerTransport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->messengerTransport = $this->container->get('messenger.transport.async');
    }

    #[DataProviderExternal(SendNotificationDataProvider::class, 'validDataCases')]
    public function testSendNotification(array $params): void
    {
        $responseData = $this->getSuccessfulResponseData($this->client,'POST', '/api/notification', $params);
        $this->assertEquals(['status' => 'queued'], $responseData);
        
        $envelopes = $this->messengerTransport->getSent();
        $this->assertCount(1, $envelopes);
        $command = $envelopes[0]->getMessage();

        $this->assertInstanceOf(SendNotificationCommand::class, $command);
        $this->assertEquals($params['recipient']['customer_identifier'], $command->getRecipient()->getCustomerIdentifier());
        $this->assertEquals($params['recipient']['email'], $command->getRecipient()->getEmail());
        $this->assertEquals($params['recipient']['phone'], $command->getRecipient()->getPhone());
        $this->assertEquals($params['notification_type'], $command->getNotificationType());
        $this->assertEquals($params['notification_data'], $command->getNotificationData());
    }

    #[DataProviderExternal(SendNotificationDataProvider::class, 'validationDataCases')]
    public function testSendNotificationValidation(array $params, array $expectedErrors): void
    {
        $this->assertPathValidation($this->client, 'POST', '/api/notification', $params, $expectedErrors);
        $this->assertCount(0, $this->messengerTransport->getSent());
    }
}
