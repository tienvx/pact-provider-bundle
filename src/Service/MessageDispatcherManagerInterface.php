<?php

namespace Tienvx\Bundle\PactProviderBundle\Service;

use Tienvx\Bundle\PactProviderBundle\Model\Message;

interface MessageDispatcherManagerInterface
{
    public function dispatch(string $description): ?Message;
}
