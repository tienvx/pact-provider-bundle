<?php

namespace Tienvx\Bundle\PactProviderBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tienvx\Bundle\PactProviderBundle\Enum\Action;
use Tienvx\Bundle\PactProviderBundle\Exception\BadRequestException;
use Tienvx\Bundle\PactProviderBundle\Model\ProviderState;
use Tienvx\Bundle\PactProviderBundle\Model\StateValues;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManagerInterface;

class StateChangeController implements ControllerInterface
{
    public function __construct(
        private StateHandlerManagerInterface $stateHandlerManager,
        private bool $body
    ) {
    }

    public function handle(Request $request): ?Response
    {
        $values = $this->handleProviderState($this->getProviderState($request), $this->getAction($request));

        if ($values) {
            return JsonResponse::fromJsonString($values);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function getProviderState(Request $request): ProviderState
    {
        if ($this->body) {
            $body = $request->toArray();
            $state = $body['state'] ?? null;
            $params = $body['params'] ?? [];
        } else {
            $params = $request->query->all();
            $state = $params['state'] ?? null;
            foreach (['state', 'action'] as $key) {
                unset($params[$key]);
            }
        }
        if (!is_string($state)) {
            throw new BadRequestException("'state' is missing or invalid in state change request.");
        }
        if (!is_array($params)) {
            throw new BadRequestException("'params' is invalid in state change request.");
        }

        return new ProviderState($state, $params);
    }

    private function getAction(Request $request): string
    {
        if ($this->body) {
            $action = $request->toArray()['action'] ?? null;
        } else {
            $action = $request->query->all()['action'] ?? null;
        }
        if (!is_string($action) || !in_array($action, Action::all())) {
            throw new BadRequestException("'action' is missing or invalid in state change request.");
        }

        return $action;
    }

    private function handleProviderState(ProviderState $providerState, string $action): ?StateValues
    {
        return $this->stateHandlerManager->handle($providerState->state, $action, $providerState->params);
    }
}
