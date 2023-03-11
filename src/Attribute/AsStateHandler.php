<?php

namespace Tienvx\Bundle\PactProviderBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsStateHandler
{
    public function __construct(public string $state)
    {
    }
}
