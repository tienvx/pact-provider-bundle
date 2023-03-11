<?php

namespace Tienvx\Bundle\PactProviderBundle\MessageDispatcher;

use Tienvx\Bundle\PactProviderBundle\Model\Message;

interface DispatcherInterface
{
    public function dispatch(): Message;
}
