<?php

namespace Tienvx\Bundle\PactProviderBundle\Service;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Tienvx\Bundle\PactProviderBundle\Exception\LogicException;
use Tienvx\Bundle\PactProviderBundle\Exception\NoDispatcherForMessageException;
use Tienvx\Bundle\PactProviderBundle\MessageDispatcher\DispatcherInterface;
use Tienvx\Bundle\PactProviderBundle\Model\Message;

class MessageDispatcherManager implements MessageDispatcherManagerInterface
{
    public function __construct(private ServiceLocator $locator)
    {
    }

    public function dispatch(string $description): Message
    {
        if (!$this->locator->has($description)) {
            throw new NoDispatcherForMessageException(sprintf("No dispatcher for description '%s'.", $description));
        }
        $dispatcher = $this->locator->get($description);
        if (!$dispatcher instanceof DispatcherInterface) {
            throw new LogicException(sprintf('Handler "%s" must implement "%s".', get_debug_type($dispatcher), DispatcherInterface::class));
        }

        return $dispatcher->dispatch();
    }
}
