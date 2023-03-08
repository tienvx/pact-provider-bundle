<?php

namespace Tienvx\Bundle\MbtBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tienvx\Bundle\PactProviderBundle\DependencyInjection\TienvxPactProviderExtension;
use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;

class TienvxPactProviderExtensionTest extends TestCase
{
    protected ContainerBuilder $container;
    protected TienvxPactProviderExtension $loader;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->loader = new TienvxPactProviderExtension();
    }

    public function testLoad(): void
    {
        $this->loader->load([
            ['state_change' => ['url' => '/path/to/state/change']],
        ], $this->container);
        $this->assertTrue($this->container->has(StateChangeRequestListener::class));
        $definition = $this->container->getDefinition(StateChangeRequestListener::class);
        $this->assertTrue($definition->hasTag('kernel.event_listener'));
        $this->assertInstanceOf(
            ServiceLocatorArgument::class,
            $definition->getArgument(0)
        );
        $this->assertEquals(
            '/path/to/state/change',
            $definition->getArgument(1)
        );
    }
}
