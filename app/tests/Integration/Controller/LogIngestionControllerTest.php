<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Message\LogEntryMessage;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class LogIngestionControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testSuccessfulIngestReturns202AndDispatchesMessages(): void
    {
        $payload = [
            'logs' => [
                [
                    'timestamp' => '2026-02-26T10:30:45Z',
                    'level' => 'error',
                    'service' => 'auth-service',
                    'message' => 'Auth failed',
                    'context' => ['user_id' => 123],
                    'trace_id' => 'abc123',
                ],
                [
                    'timestamp' => '2026-02-26T10:30:46Z',
                    'level' => 'info',
                    'service' => 'api-gateway',
                    'message' => 'Request processed',
                ],
            ],
        ];

        $this->client->request('POST', '/api/logs/ingest', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $response = $this->client->getResponse();
        $this->assertSame(202, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertSame('accepted', $body['status']);
        $this->assertSame(2, $body['logs_count']);

        $transport = $this->getContainer()->get('messenger.transport.logs_ingest');
        $messages = $transport->getSent();

        $this->assertCount(2, $messages);
    }

    public function testValidationErrorReturns400(): void
    {
        $payload = [
            'logs' => [
                ['level' => 'error', 'service' => 'auth-service'],
            ],
        ];

        $this->client->request('POST', '/api/logs/ingest', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $this->assertSame(400, $this->client->getResponse()->getStatusCode());

        $transport = $this->getContainer()->get('messenger.transport.logs_ingest');
        $this->assertCount(0, $transport->getSent());
    }
}
