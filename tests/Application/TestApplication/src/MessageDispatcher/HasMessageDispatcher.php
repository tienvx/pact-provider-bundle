<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Application\TestApplication\MessageDispatcher;

use Tienvx\Bundle\PactProviderBundle\Attribute\AsMessageDispatcher;
use Tienvx\Bundle\PactProviderBundle\MessageDispatcher\DispatcherInterface;
use Tienvx\Bundle\PactProviderBundle\Model\Message;

#[AsMessageDispatcher(description: 'has message')]
class HasMessageDispatcher implements DispatcherInterface
{
    public function __construct()
    {
    }

    public function dispatch(): ?Message
    {
        return new Message(
            'message content',
            'text/plain',
            json_encode(['key' => 'value', 'contentType' => 'text/plain'])
        );
    }
}
