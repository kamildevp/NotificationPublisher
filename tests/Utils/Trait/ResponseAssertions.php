<?php

declare(strict_types=1);

namespace App\Tests\Utils\Trait;

trait ResponseAssertions
{
    protected function assertSuccessfulResponse(array $decodedResponse): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertJsonResponse();
        $this->assertEquals('success', $decodedResponse['status']);
        $this->assertArrayHasKey('data', $decodedResponse);
    }

    protected function assertFailureResponse(array $decodedResponse): void
    {
        $this->assertEquals('fail', $decodedResponse['status']);
        $this->assertArrayHasKey('data', $decodedResponse);
    }

    protected function assertValidationErrorResponse(array $decodedResponse): void
    {
        $this->assertFailureResponse($decodedResponse);
        $this->assertEquals('Validation Error', $decodedResponse['data']['message']);
        $this->assertArrayHasKey('errors', $decodedResponse['data']);
    }

    protected function assertJsonResponse(): void
    {
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }
}