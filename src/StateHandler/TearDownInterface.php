<?php

namespace Tienvx\Bundle\PactProviderBundle\StateHandler;

interface TearDownInterface
{
    public function tearDown(array $params): void;
}
