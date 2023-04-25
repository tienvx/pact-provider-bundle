<?php

namespace Tienvx\Bundle\PactProviderBundle\Service;

use Tienvx\Bundle\PactProviderBundle\Model\StateValues;

interface StateHandlerManagerInterface
{
    public function handle(string $state, string $action, array $params): ?StateValues;
}
