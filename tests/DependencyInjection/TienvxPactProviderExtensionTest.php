<?php

namespace Tienvx\Bundle\MbtBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tienvx\Bundle\PactProviderBundle\DependencyInjection\TienvxPactProviderExtension;
use Tienvx\Bundle\PactProviderBundle\EventListener\DispatchMessageRequestListener;
use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManager;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManagerInterface;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManager;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManagerInterface;

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
            [
                'state_change' => [
                    'url' => '/path/to/pact-change-state',
                    'body' => false,
                ],
                'messages_url' => '/path/to/pact-messages',
            ],
        ], $this->container);
        $services = [
            StateHandlerManager::class => [
                'alias' => StateHandlerManagerInterface::class,
                'args' => fn (array $args) => 1 === count($args) && $args[0] instanceof ServiceLocatorArgument,
            ],
            MessageDispatcherManager::class => [
                'alias' => MessageDispatcherManagerInterface::class,
                'args' => fn (array $args) => 1 === count($args) && $args[0] instanceof ServiceLocatorArgument,
            ],
            StateChangeRequestListener::class => [
                'tag' => 'kernel.event_listener',
                'args' => function (array $args): bool {
                    return 3 === count($args) &&
                        StateHandlerManagerInterface::class == $args[0] &&
                        '/path/to/pact-change-state' === $args[1] &&
                        false === $args[2];
                },
            ],
            DispatchMessageRequestListener::class => [
                'tag' => 'kernel.event_listener',
                'args' => function (array $args): bool {
                    return 3 === count($args) &&
                        StateHandlerManagerInterface::class == $args[0] &&
                        MessageDispatcherManagerInterface::class == $args[1] &&
                        '/path/to/pact-messages' === $args[2];
                },
            ],
        ];
        foreach ($services as $key => $value) {
            $this->assertTrue($this->container->has($key));
            $definition = $this->container->getDefinition($key);
            if (isset($value['tag'])) {
                $this->assertTrue($definition->hasTag($value['tag']));
            }
            $this->assertTrue($value['args']($definition->getArguments()));
            if (isset($value['alias'])) {
                $this->assertEquals($key, $this->container->getAlias($value['alias']));
            }
        }
    }
}
