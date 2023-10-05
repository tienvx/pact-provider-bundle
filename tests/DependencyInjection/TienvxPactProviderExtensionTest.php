<?php

namespace Tienvx\Bundle\MbtBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tienvx\Bundle\PactProviderBundle\Controller\MessagesController;
use Tienvx\Bundle\PactProviderBundle\Controller\StateChangeController;
use Tienvx\Bundle\PactProviderBundle\DependencyInjection\TienvxPactProviderExtension;
use Tienvx\Bundle\PactProviderBundle\EventListener\MessagesRequestListener;
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
            MessagesController::class => [
                'args' => fn (array $args) => 2 === count($args)
                    && StateHandlerManagerInterface::class == $args[0]
                    && MessageDispatcherManagerInterface::class == $args[1],
            ],
            StateChangeController::class => [
                'args' => fn (array $args) => 2 === count($args)
                    && StateHandlerManagerInterface::class == $args[0]
                    && false == $args[1],
            ],
            StateChangeRequestListener::class => [
                'tag' => 'kernel.event_listener',
                'args' => function (array $args): bool {
                    return 2 === count($args)
                        && StateChangeController::class == $args[0]
                        && '/path/to/pact-change-state' === $args[1];
                },
            ],
            MessagesRequestListener::class => [
                'tag' => 'kernel.event_listener',
                'args' => function (array $args): bool {
                    return 2 === count($args)
                        && MessagesController::class == $args[0]
                        && '/path/to/pact-messages' === $args[1];
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
