<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\TestApplication\StateHandler;

use Tienvx\Bundle\PactProviderBundle\Attribute\AsStateHandler;
use Tienvx\Bundle\PactProviderBundle\Model\StateValues;
use Tienvx\Bundle\PactProviderBundle\StateHandler\SetUpInterface;
use Tienvx\Bundle\PactProviderBundle\StateHandler\TearDownInterface;

#[AsStateHandler(state: 'has values')]
class HasValuesHandler implements SetUpInterface, TearDownInterface
{
    public function setUp(array $params): ?StateValues
    {
        return new StateValues([
            'id' => 123,
        ]);
    }

    public function tearDown(array $params): void
    {
    }
}
