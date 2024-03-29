<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\TestApplication\StateHandler;

use Tienvx\Bundle\PactProviderBundle\Attribute\AsStateHandler;
use Tienvx\Bundle\PactProviderBundle\Model\StateValues;
use Tienvx\Bundle\PactProviderBundle\StateHandler\SetUpInterface;
use Tienvx\Bundle\PactProviderBundle\StateHandler\TearDownInterface;

#[AsStateHandler(state: 'no values')]
class NoValuesHandler implements SetUpInterface, TearDownInterface
{
    public function setUp(array $params): ?StateValues
    {
        return null;
    }

    public function tearDown(array $params): void
    {
    }
}
