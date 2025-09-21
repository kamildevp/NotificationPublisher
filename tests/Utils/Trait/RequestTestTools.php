<?php

declare(strict_types=1);

namespace App\Tests\Utils\Trait;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait RequestTestTools
{
    protected function jsonRequest(
        KernelBrowser $client,
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
    ) {
        $method = strtoupper($method);

        $headers = [
            'HTTP_ACCEPT' => 'application/json',
        ];

        if(!empty($authHeader = $client->getServerParameter('HTTP_AUTHORIZATION'))){
            $headers = array_merge($headers, ['HTTP_AUTHORIZATION' => $authHeader]);
        }

        if ($method !== 'GET') {
            $headers['CONTENT_TYPE'] = 'application/json';
        }

        $content = $method !== 'GET' && !$content ? json_encode($parameters) : $content;
        $query = $method === 'GET' ? $parameters : [];

        return $client->request(
            $method,
            $uri,
            $query, 
            $files,
            array_merge($headers, $server),
            $content
        );
    }
}