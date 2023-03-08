<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(StateChangeRequestListener::class)
            ->args([
                tagged_locator('pact_provider.state_change_handler', 'state'),
                '',
            ])
            ->tag('kernel.event_listener')
    ;
};
