<?php

namespace Tienvx\Bundle\PactProviderBundle\Model;

class ProviderState
{
    public function __construct(
        public readonly string $state,
        public readonly array $params
    ) {
    }
}
