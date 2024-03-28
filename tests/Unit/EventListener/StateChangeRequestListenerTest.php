<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Unit\EventListener;

use Tienvx\Bundle\PactProviderBundle\EventListener\AbstractRequestListener;
use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;

class StateChangeRequestListenerTest extends AbstractRequestListenerTestCase
{
    protected function createListener(): AbstractRequestListener
    {
        return new StateChangeRequestListener($this->controller, $this->url);
    }
}
