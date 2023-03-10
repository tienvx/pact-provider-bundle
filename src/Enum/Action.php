<?php

namespace Tienvx\Bundle\PactProviderBundle\Enum;

class Action
{
    public const SETUP = 'setup';
    public const TEARDOWN = 'teardown';

    public static function all(): array
    {
        return [self::SETUP, self::TEARDOWN];
    }
}
