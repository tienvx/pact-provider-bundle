<?php

namespace Tienvx\Bundle\PactProviderBundle\StateHandler;

interface SetUpInterface
{
    public function setUp(array $params): void;
}
