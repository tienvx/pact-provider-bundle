<?php

namespace Tienvx\Bundle\PactProviderBundle\Service;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Tienvx\Bundle\PactProviderBundle\Enum\Action;
use Tienvx\Bundle\PactProviderBundle\Exception\LogicException;
use Tienvx\Bundle\PactProviderBundle\Exception\NoHandlerForStateException;
use Tienvx\Bundle\PactProviderBundle\Model\StateValues;
use Tienvx\Bundle\PactProviderBundle\StateHandler\SetUpInterface;
use Tienvx\Bundle\PactProviderBundle\StateHandler\TearDownInterface;

class StateHandlerManager implements StateHandlerManagerInterface
{
    public function __construct(private ServiceLocator $locator)
    {
    }

    public function handle(string $state, string $action, array $params): ?StateValues
    {
        if (!$this->locator->has($state)) {
            throw new NoHandlerForStateException(sprintf("No handler for state '%s'.", $state));
        }
        $handler = $this->locator->get($state);
        switch ($action) {
            case Action::SETUP:
                if (!$handler instanceof SetUpInterface) {
                    throw new LogicException(sprintf('Handler "%s" must implement "%s".', get_debug_type($handler), SetUpInterface::class));
                }

                return $handler->setUp($params);

            case Action::TEARDOWN:
                if (!$handler instanceof TearDownInterface) {
                    throw new LogicException(sprintf('Handler "%s" must implement "%s".', get_debug_type($handler), TearDownInterface::class));
                }
                $handler->tearDown($params);
                break;

            default:
                break;
        }

        return null;
    }
}
