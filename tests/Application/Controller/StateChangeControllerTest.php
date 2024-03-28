<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Application\Controller;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tienvx\Bundle\PactProviderBundle\Enum\Action;

class StateChangeControllerTest extends WebTestCase
{
    public function testWrongUrl(): void
    {
        $client = static::createClient();
        $client->request('POST', '/pact-change-state-not-found');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testRequestBodyIsEmpty(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state');
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Request body is empty.', $client->getResponse()->getContent());
    }

    #[TestWith([[]])]
    #[TestWith([['state' => 123]])]
    public function testMissingOrInvalidProviderStateNameInBody(array $value): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state', [], [], [], json_encode([
            'params' => [
                'key' => 'value',
            ],
            'action' => Action::SETUP,
            ...$value,
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'state' is missing or invalid in state change request.", $client->getResponse()->getContent());
    }

    public function testInvalidProviderStateParamsInBody(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state', [], [], [], json_encode([
            'state' => 'required state',
            'params' => 123,
            'action' => Action::SETUP,
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'params' is invalid in state change request.", $client->getResponse()->getContent());
    }

    public function testMissingProviderStateActionInBody(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state', [], [], [], json_encode([
            'state' => 'required state',
            'params' => [
                'key' => 'value',
            ],
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'action' is missing in state change request.", $client->getResponse()->getContent());
    }

    public function testInvalidProviderStateActionInBody(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state', [], [], [], json_encode([
            'state' => 'required state',
            'params' => [
                'key' => 'value',
            ],
            'action' => 'clean',
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'action' is invalid in state change request.", $client->getResponse()->getContent());
    }

    #[TestWith([Action::SETUP])]
    #[TestWith([Action::TEARDOWN])]
    public function testNoStateValuesWithBody(Action $action): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state', [], [], [], json_encode([
            'state' => 'no values',
            'params' => [
                'key' => 'value',
            ],
            'action' => $action,
        ]));
        $this->assertResponseStatusCodeSame(204);
        $this->assertEmpty($client->getResponse()->getContent());
    }

    public function testHasValuesWithBody(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state', [], [], [], json_encode([
            'state' => 'required state',
            'params' => [
                'key' => 'value',
            ],
            'action' => Action::SETUP,
        ]));
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertStringContainsString(json_encode([
            'id' => 123,
        ]), $client->getResponse()->getContent());
    }

    #[TestWith([[]])]
    #[TestWith([['state[]' => 'required state']])]
    public function testMissingOrInvalidProviderStateNameInQuery(array $value): void
    {
        $_ENV['STATE_CHANGE_BODY'] = 'false';
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state?'.http_build_query([
            'key' => 'value',
            'action' => Action::SETUP->value,
            ...$value,
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'state' is missing or invalid in state change request.", $client->getResponse()->getContent());
    }

    public function testMissingProviderStateActionInQuery(): void
    {
        $_ENV['STATE_CHANGE_BODY'] = 'false';
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state?'.http_build_query([
            'state' => 'required state',
            'key' => 'value',
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'action' is missing in state change request.", $client->getResponse()->getContent());
    }

    public function testInvalidProviderStateActionInQuery(): void
    {
        $_ENV['STATE_CHANGE_BODY'] = 'false';
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state?'.http_build_query([
            'state' => 'required state',
            'key' => 'value',
            'action' => 'clean',
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString("'action' is invalid in state change request.", $client->getResponse()->getContent());
    }

    #[TestWith([Action::SETUP])]
    #[TestWith([Action::TEARDOWN])]
    public function testNoStateValuesWithoutBody(Action $action): void
    {
        $_ENV['STATE_CHANGE_BODY'] = 'false';
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state?'.http_build_query([
            'state' => 'no values',
            'key' => 'value',
            'action' => $action->value,
        ]));
        $this->assertResponseStatusCodeSame(204);
        $this->assertEmpty($client->getResponse()->getContent());
    }

    public function testHasValuesWithoutBody(): void
    {
        $_ENV['STATE_CHANGE_BODY'] = 'false';
        $client = static::createClient();
        $client->request('POST', '/test-pact-change-state?'.http_build_query([
            'state' => 'required state',
            'key' => 'value',
            'action' => Action::SETUP->value,
        ]));
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertStringContainsString(json_encode([
            'id' => 123,
        ]), $client->getResponse()->getContent());
    }
}
