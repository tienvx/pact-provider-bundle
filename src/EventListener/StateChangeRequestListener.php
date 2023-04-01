<?php

namespace Tienvx\Bundle\PactProviderBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tienvx\Bundle\PactProviderBundle\Enum\Action;
use Tienvx\Bundle\PactProviderBundle\Exception\BadRequestException;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManagerInterface;

class StateChangeRequestListener
{
    public function __construct(
        private StateHandlerManagerInterface $stateHandlerManager,
        private string $url,
        private bool $body
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if ($request->getPathInfo() === $this->url && Request::METHOD_POST === $request->getMethod()) {
            [$state, $action, $params] = $this->getParameters($request);

            $this->stateHandlerManager->handle($state, $action, $params);

            $event->setResponse(new Response('', Response::HTTP_NO_CONTENT));
            $event->stopPropagation();
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
        if (!is_string($action) || !in_array($action, Action::all())) {
            throw new BadRequestException("'action' is missing or invalid in state change request.");
        }
        if (!is_array($params)) {
            throw new BadRequestException("'params' is missing or invalid in state change request.");
        }

        return [$state, $action, $params];
    }
}
