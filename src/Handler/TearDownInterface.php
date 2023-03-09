<?php

namespace Tienvx\Bundle\PactProviderBundle\Handler;

interface TearDownInterface
{
    public function tearDown(array $params): void;
}
