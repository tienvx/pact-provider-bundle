<?php

namespace Tienvx\Bundle\PactProviderBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Tienvx\Bundle\PactProviderBundle\Attribute\AsMessageDispatcher;
use Tienvx\Bundle\PactProviderBundle\Attribute\AsStateHandler;
use Tienvx\Bundle\PactProviderBundle\Controller\StateChangeController;
use Tienvx\Bundle\PactProviderBundle\EventListener\MessagesRequestListener;
use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;

class TienvxPactProviderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        if (method_exists($container, 'registerAttributeForAutoconfiguration')) {
            $container->registerAttributeForAutoconfiguration(
                AsStateHandler::class,
                static function (ChildDefinition $definition, AsStateHandler $attribute, \Reflector $reflector): void {
                    $tagAttributes = get_object_vars($attribute);
                    $definition->addTag('pact_provider.state_handler', $tagAttributes);
                }
            );

            $container->registerAttributeForAutoconfiguration(
                AsMessageDispatcher::class,
                static function (ChildDefinition $definition, AsMessageDispatcher $attribute, \Reflector $reflector): void {
                    $tagAttributes = get_object_vars($attribute);
                    $definition->addTag('pact_provider.message_dispatcher', $tagAttributes);
                }
            );
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(StateChangeController::class);
        $definition->replaceArgument(1, $config['state_change']['body']);

        $definition = $container->getDefinition(StateChangeRequestListener::class);
        $definition->replaceArgument(1, $config['state_change']['url']);

        $definition = $container->getDefinition(MessagesRequestListener::class);
        $definition->replaceArgument(1, $config['messages_url']);
    }
}
