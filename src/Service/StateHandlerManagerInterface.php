<?php

namespace Tienvx\Bundle\PactProviderBundle\Service;

interface StateHandlerManagerInterface
{
    public function handle(string $state, string $action, array $params): void;
}
