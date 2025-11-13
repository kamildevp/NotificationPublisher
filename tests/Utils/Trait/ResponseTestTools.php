<?php

declare(strict_types=1);

namespace App\Tests\Utils\Trait;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ResponseTestTools
{
    /**
     * @return mixed[]
     */
    protected function getJsonResponse(KernelBrowser $client): array
    {
        $this->assertJsonResponse();
        $responseContent = $client->getResponse()->getContent();
        return json_decode($responseContent, true);
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $files
     * @param array<string,string> $server
     * @return mixed[]
     */
    protected function getFailureResponseData(
        KernelBrowser $client,
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        int $expectedCode = 422,
    ): array
    {
        $this->jsonRequest($client, $method, $uri, $parameters, $files, $server);
        
        $decodedResponse = $this->getJsonResponse($client);
        $this->assertResponseStatusCodeSame($expectedCode);
        $this->assertFailureResponse($decodedResponse);
        return $decodedResponse['data'];
    }

    /**
     * @param mixed[] $parameters
     * @param mixed[] $files
     * @param array<string,string> $server
     * @return mixed[]
     */
    protected function getSuccessfulResponseData(
        KernelBrowser $client,
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = []
    ): array
    {
        $this->jsonRequest($client, $method, $uri, $parameters, $files, $server);
        
        $decodedResponse = $this->getJsonResponse($client);
        $this->assertSuccessfulResponse($decodedResponse);
        return $decodedResponse['data'];
    }
}