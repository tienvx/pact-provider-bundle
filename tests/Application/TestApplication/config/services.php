<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $container->parameters()->set('env(STATE_CHANGE_BODY)', 'true');

    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services->load('Tienvx\\Bundle\\PactProviderBundle\\Tests\\Application\\TestApplication\\', '../src/*')
        ->exclude('../{Entity,Tests,Kernel.php}');
};
