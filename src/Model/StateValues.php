<?php

namespace Tienvx\Bundle\PactProviderBundle\Model;

class StateValues
{
    public function __construct(
        public readonly array $values
    ) {
    }

    public function __toString(): string
    {
        return \json_encode($this->values);
    }
}
