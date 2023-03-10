<?php

namespace Tienvx\Bundle\PactProviderBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Tienvx\Bundle\PactProviderBundle\Attribute\AsMessageDispatcher;
use Tienvx\Bundle\PactProviderBundle\Attribute\AsStateHandler;
use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;

class TienvxPactProviderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $container->registerAttributeForAutoconfiguration(
            AsStateHandler::class,
            static function (ChildDefinition $definition, AsStateHandler $attribute, \ReflectionClass $reflector): void {
                $tagAttributes = get_object_vars($attribute);
                $definition->addTag('pact_provider.state_handler', $tagAttributes);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsMessageDispatcher::class,
            static function (ChildDefinition $definition, AsMessageDispatcher $attribute, \ReflectionClass $reflector): void {
                $tagAttributes = get_object_vars($attribute);
                $definition->addTag('pact_provider.message_dispatcher', $tagAttributes);
            }
        );

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(StateChangeRequestListener::class);
        $definition->replaceArgument(1, $config['state_change']['url']);
        $definition->replaceArgument(2, $config['state_change']['body']);
    }
}
