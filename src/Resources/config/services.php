<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tienvx\Bundle\PactProviderBundle\Controller\MessagesController;
use Tienvx\Bundle\PactProviderBundle\Controller\StateChangeController;
use Tienvx\Bundle\PactProviderBundle\EventListener\MessagesRequestListener;
use Tienvx\Bundle\PactProviderBundle\EventListener\StateChangeRequestListener;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManager;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManagerInterface;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManager;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManagerInterface;

return static function (ContainerConfigurator $container): void {
    $namespace = __NAMESPACE__;
    $service = function_exists("$namespace\\service") ? "$namespace\\service" : "$namespace\\ref";
    $container->services()
        ->set(StateHandlerManager::class)
            ->args([
                tagged_locator('pact_provider.state_handler', 'state'),
            ])
            ->alias(StateHandlerManagerInterface::class, StateHandlerManager::class)

        ->set(MessageDispatcherManager::class)
            ->args([
                tagged_locator('pact_provider.message_dispatcher', 'description'),
            ])
            ->alias(MessageDispatcherManagerInterface::class, MessageDispatcherManager::class)

        ->set(MessagesController::class)
            ->args([
                $service(StateHandlerManagerInterface::class),
                true,
            ])

        ->set(StateChangeController::class)
            ->args([
                $service(StateHandlerManagerInterface::class),
                $service(MessageDispatcherManagerInterface::class),
            ])

        ->set(StateChangeRequestListener::class)
            ->args([
                $service(StateChangeController::class),
                '',
            ])
            // Before Symfony\Component\HttpKernel\EventListener\RouterListener::onKernelRequest
            ->tag('kernel.event_listener', ['priority' => 33])
        ->set(MessagesRequestListener::class)
            ->args([
                $service(MessagesController::class),
                '',
            ])
            // Before Symfony\Component\HttpKernel\EventListener\RouterListener::onKernelRequest
            ->tag('kernel.event_listener', ['priority' => 33])
    ;
};
