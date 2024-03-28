<?php

namespace Tienvx\Bundle\PactProviderBundle\Enum;

enum Action: string
{
    case SETUP = 'setup';
    case TEARDOWN = 'teardown';
}
