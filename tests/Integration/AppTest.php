<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    private Client $httpClient;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        exec('composer db-recreate');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new Client();
    }

    public function testCreateJob()
    {
        $response = $this->httpClient->post('http://localhost:9090/create', [
            'form_params' => [
                'label' => md5('label content'),
                'callback-url' => 'http://example.com/callback',
                'maximum-duration-in-seconds' => 600,
            ],
        ]);

        self::assertSame(200, $response->getStatusCode());
    }

    public function testGetJobStatus()
    {
        $response = $this->httpClient->get('http://localhost:9090/status');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        $body = $response->getBody()->getContents();
        $bodyData = json_decode($body, true);

        self::assertSame(
            [
                'label' => md5('label content'),
                'callback_url' => 'http://example.com/callback',
                'maximum_duration_in_seconds' => 600,
                'sources' => [],
                'compilation_state' => 'awaiting',
                'execution_state' => 'awaiting',
                'tests' => [],
            ],
            $bodyData
        );
    }
}
