<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Integration\Service;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tienvx\Bundle\PactProviderBundle\Enum\Action;
use Tienvx\Bundle\PactProviderBundle\Exception\NoHandlerForStateException;
use Tienvx\Bundle\PactProviderBundle\Model\StateValues;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManagerInterface;

class StateHandlerManagerTest extends KernelTestCase
{
    #[TestWith(['no values', Action::SETUP, null])]
    #[TestWith(['no values', Action::TEARDOWN, null])]
    #[TestWith(['has values', Action::SETUP, new StateValues(['id' => 123])])]
    #[TestWith(['has values', Action::TEARDOWN, null])]
    public function testHandle(string $state, Action $action, ?StateValues $stateValues): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $manager = $container->get(StateHandlerManagerInterface::class);
        $this->assertEquals($stateValues, $manager->handle($state, $action, ['key' => 'value']));
    }

    #[TestWith([Action::SETUP])]
    #[TestWith([Action::TEARDOWN])]
    public function testMissingHandler(Action $action): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $manager = $container->get(StateHandlerManagerInterface::class);

        $state = 'no handler';

        $this->expectException(NoHandlerForStateException::class);
        $this->expectExceptionMessage(sprintf("No handler for state '%s'.", $state));

        $manager->handle($state, $action, ['key' => 'value']);
    }
}
