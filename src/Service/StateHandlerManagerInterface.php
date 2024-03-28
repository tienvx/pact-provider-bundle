<?php

namespace Tienvx\Bundle\PactProviderBundle\Service;

use Tienvx\Bundle\PactProviderBundle\Enum\Action;
use Tienvx\Bundle\PactProviderBundle\Model\StateValues;

interface StateHandlerManagerInterface
{
    public function handle(string $state, Action $action, array $params): ?StateValues;
}
