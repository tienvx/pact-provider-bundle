<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\TestApplication\MessageDispatcher;

use Tienvx\Bundle\PactProviderBundle\Attribute\AsMessageDispatcher;
use Tienvx\Bundle\PactProviderBundle\MessageDispatcher\DispatcherInterface;
use Tienvx\Bundle\PactProviderBundle\Model\Message;

#[AsMessageDispatcher(description: 'no message')]
class NoMessageDispatcher implements DispatcherInterface
{
    public function __construct()
    {
    }

    public function dispatch(): ?Message
    {
        return null;
    }
}
