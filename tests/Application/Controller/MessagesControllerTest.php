<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Application\Controller;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessagesControllerTest extends WebTestCase
{
    public function testWrongUrl(): void
    {
        $client = static::createClient();
        $client->request('POST', '/pact-messages-not-found');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testRequestBodyIsEmpty(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-messages');
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Request body is empty.', $client->getResponse()->getContent());
    }

    #[TestWith([null])]
    #[TestWith([[]])]
    public function testMissingOrInvalidProviderStates(mixed $value): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-messages', [], [], [], json_encode([
            'providerStates' => $value,
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'providerStates' is missing or invalid in messages request.", $client->getResponse()->getContent());
    }

    public function testMissingProviderStateName(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-messages', [], [], [], json_encode([
            'providerStates' => [
                [
                    'params' => [
                        'key' => 'value',
                    ],
                ],
            ],
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("Invalid or missing 'name' for provider state.", $client->getResponse()->getContent());
    }

    public function testInvalidProviderStateName(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-messages', [], [], [], json_encode([
            'providerStates' => [
                [
                    'name' => 123,
                    'params' => [
                        'key' => 'value',
                    ],
                ],
            ],
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("Invalid or missing 'name' for provider state.", $client->getResponse()->getContent());
    }

    public function testInvalidProviderStateParams(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-messages', [], [], [], json_encode([
            'providerStates' => [
                [
                    'name' => 'required state',
                    'params' => 123,
                ],
            ],
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("Invalid 'params' for provider state 'required state'.", $client->getResponse()->getContent());
    }

    public function testNoMessage(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-messages', [], [], [], json_encode([
            'description' => 'no message',
            'providerStates' => [
                [
                    'name' => 'required state',
                    'params' => [
                        'key' => 'value',
                    ],
                ],
            ],
        ]));
        // @todo Check this behavior
        $this->assertResponseStatusCodeSame(404);
        $this->assertStringContainsString('No route found', $client->getResponse()->getContent());
    }

    public function testHasMessage(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-messages', [], [], [], json_encode([
            'description' => 'has message',
            'providerStates' => [
                [
                    'name' => 'required state',
                    'params' => [
                        'key' => 'value',
                    ],
                ],
            ],
        ]));
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertResponseHeaderSame('Pact-Message-Metadata', 'eyJrZXkiOiJ2YWx1ZSIsImNvbnRlbnRUeXBlIjoidGV4dFwvcGxhaW4ifQ==');
        $this->assertStringContainsString('message content', $client->getResponse()->getContent());
    }
}
