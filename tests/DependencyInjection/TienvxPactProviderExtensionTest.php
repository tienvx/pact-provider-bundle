<?php

namespace Tienvx\Bundle\MbtBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
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
            ['state_change' => ['url' => '/path/to/state/change']]
        ], $this->container);
        $this->assertTrue($this->container->has(StateChangeRequestListener::class));
        $this->assertTrue(
            $this->container
                ->getDefinition(StateChangeRequestListener::class)
                ->hasTag('kernel.event_listener')
        );
    }
}
