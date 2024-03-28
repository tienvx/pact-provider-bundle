<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Unit\EventListener;

use Tienvx\Bundle\PactProviderBundle\EventListener\AbstractRequestListener;
use Tienvx\Bundle\PactProviderBundle\EventListener\MessagesRequestListener;

class MessagesRequestListenerTest extends AbstractRequestListenerTestCase
{
    protected function createListener(): AbstractRequestListener
    {
        return new MessagesRequestListener($this->controller, $this->url);
    }
}
