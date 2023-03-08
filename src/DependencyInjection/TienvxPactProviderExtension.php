<?php

namespace Tienvx\Bundle\PactProviderBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Tienvx\Bundle\PactProviderBundle\Attribute\AsStateChangeHandler;
use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;

class TienvxPactProviderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $container->registerAttributeForAutoconfiguration(
            AsStateChangeHandler::class,
            static function (ChildDefinition $definition, AsStateChangeHandler $attribute, \ReflectionClass $reflector): void {
                $tagAttributes = get_object_vars($attribute);
                $definition->addTag('pact_provider.state_change_handler', $tagAttributes);
            }
        );

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(StateChangeRequestListener::class);
        $definition->replaceArgument(1, $config['state_change']['url']);
    }
}
