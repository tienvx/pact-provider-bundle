<?php

namespace Tienvx\Bundle\PactProviderBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsMessageDispatcher
{
    public function __construct(public string $description)
    {
    }
}
