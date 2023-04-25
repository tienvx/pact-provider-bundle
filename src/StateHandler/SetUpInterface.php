<?php

namespace Tienvx\Bundle\PactProviderBundle\StateHandler;

use Tienvx\Bundle\PactProviderBundle\Model\StateValues;

interface SetUpInterface
{
    public function setUp(array $params): ?StateValues;
}
