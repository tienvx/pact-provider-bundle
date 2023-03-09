<?php

namespace Tienvx\Bundle\PactProviderBundle\EventListener;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tienvx\Bundle\PactProviderBundle\Exception\BadRequestException;
use Tienvx\Bundle\PactProviderBundle\Exception\LogicException;
use Tienvx\Bundle\PactProviderBundle\Exception\NoHandlerForStateException;
use Tienvx\Bundle\PactProviderBundle\Handler\SetUpInterface;
use Tienvx\Bundle\PactProviderBundle\Handler\TearDownInterface;

class StateChangeRequestListener
{
    public const SETUP_ACTION = 'setup';
    public const TEARDOWN_ACTION = 'teardown';

    public function __construct(
        private ServiceLocator $locator,
        private string $url,
        private bool $body
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getPathInfo() === $this->url && Request::METHOD_GET === $request->getMethod()) {
            [$state, $action, $params] = $this->getParameters($request);

            $this->callHandler($state, $action, $params);

            $event->setResponse(new Response('', Response::HTTP_NO_CONTENT));
        }
    }

    private function callHandler(string $state, string $action, array $params): void
    {
        if (!$this->locator->has($state)) {
            throw new NoHandlerForStateException(sprintf("No handler for state '%s'.", $state));
        }
        $handler = $this->locator->get($state);
        switch ($action) {
            case self::SETUP_ACTION:
                if (!$handler instanceof SetUpInterface) {
                    throw new LogicException(sprintf('Handler "%s" must implement "%s".', get_debug_type($handler), SetUpInterface::class));
                }
                $handler->setUp($params);
                break;

            case self::TEARDOWN_ACTION:
                if (!$handler instanceof TearDownInterface) {
                    throw new LogicException(sprintf('Handler "%s" must implement "%s".', get_debug_type($handler), TearDownInterface::class));
                }
                $handler->tearDown($params);
                break;

            default:
                break;
        }
    }

    private function getParameters(Request $request): array
    {
        if ($this->body) {
            $body = $request->toArray();
            $state = $body['state'] ?? null;
            $action = $body['action'] ?? null;
            $params = $body['params'] ?? [];
        } else {
            $params = $request->query->all();
            foreach (['state', 'action'] as $key) {
                if (isset($params[$key])) {
                    $$key = $params[$key];
                    unset($params[$key]);
                } else {
                    $$key = null;
                }
            }
        }
        if (!is_string($state)) {
            throw new BadRequestException("'state' is missing or invalid in state change request.");
        }
        if (!is_string($action) || !in_array($action, [self::SETUP_ACTION, self::TEARDOWN_ACTION])) {
            throw new BadRequestException("'action' is missing or invalid in state change request.");
        }
        if (!is_array($params)) {
            throw new BadRequestException("'params' is missing or invalid in state change request.");
        }

        return [$state, $action, $params];
    }
}
