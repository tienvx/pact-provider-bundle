<?php

namespace Tienvx\Bundle\PactProviderBundle\EventListener;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tienvx\Bundle\PactProviderBundle\Exception\BadMethodCallException;
use Tienvx\Bundle\PactProviderBundle\Exception\BadRequestException;
use Tienvx\Bundle\PactProviderBundle\Exception\NoHandlerForStateException;

class StateChangeRequestListener
{
    public const SETUP_ACTION = 'setup';
    public const TEARDOWN_ACTION = 'teardown';
    public const METHODS = [
        self::SETUP_ACTION => 'setUp',
        self::TEARDOWN_ACTION => 'tearDown',
    ];

    public function __construct(
        private ServiceLocator $locator,
        private string $url,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getPathInfo() === $this->url) {
            $state = $request->get('state');
            if (!$state) {
                throw new BadRequestException("'state' is missing in state change request.");
            }
            $action = $request->get('action', self::SETUP_ACTION);
            if (!in_array($action, [self::SETUP_ACTION, self::TEARDOWN_ACTION])) {
                throw new BadRequestException(sprintf("Action '%s' is not supported in state change request.", $action));
            }
            if (!$this->locator->has($state)) {
                throw new NoHandlerForStateException(sprintf("No handler for state '%s'.", $state));
            }
            $handler = $this->locator->get($state);
            $method = [$handler, self::METHODS[$action]];
            if (!is_callable($method)) {
                throw new BadMethodCallException(sprintf("Method '%s' does not exist in '%s'.", $method[1], get_class($method[0])));
            }
            $params = $request->get('params', []);
            call_user_func($method, $params);

            $response = new Response();
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
            $event->setResponse($response);
        }
    }
}
