<?php

namespace Tienvx\Bundle\PactProviderBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tienvx\Bundle\PactProviderBundle\Enum\Action;
use Tienvx\Bundle\PactProviderBundle\Exception\BadRequestException;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManagerInterface;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManagerInterface;

class DispatchMessageRequestListener
{
    public const DISPATCH_MESSAGE_URL = '/';

    public function __construct(
        private StateHandlerManagerInterface $stateHandlerManager,
        private MessageDispatcherManagerInterface $messageDispatcherManager
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if (self::DISPATCH_MESSAGE_URL === $request->getPathInfo() && Request::METHOD_POST === $request->getMethod()) {
            [$description, $providerStates] = $this->getParameters($request);

            $this->handle($providerStates, Action::SETUP);
            $this->setReponse($event, $description);
            $this->handle($providerStates, Action::TEARDOWN);
        }
    }

    private function getParameters(Request $request): array
    {
        $body = $request->toArray();
        $description = $body['description'] ?? null;
        $providerStates = $body['providerStates'] ?? [];
        if (!is_string($description)) {
            throw new BadRequestException("'description' is missing or invalid in dispatch message request.");
        }
        if (!is_array($providerStates) || empty($providerStates)) {
            throw new BadRequestException("'providerStates' is missing or invalid in dispatch message request.");
        }

        return [$description, $providerStates];
    }

    private function handle(array $providerStates, string $action): void
    {
        foreach ($providerStates as $providerState) {
            $name = $providerState['name'] ?? null;
            if (!is_string($name)) {
                throw new BadRequestException("Missing 'name' for provider state.");
            }
            $params = $providerState['params'] ?? [];
            if (!is_array($params)) {
                throw new BadRequestException(sprintf("Invalid 'params' for provider state '%s'.", $name));
            }
            $this->stateHandlerManager->handle($providerState['name'], $action, $params);
        }
    }

    private function setReponse(RequestEvent $event, string $description): void
    {
        $message = $this->messageDispatcherManager->dispatch($description);

        if ($message) {
            $event->setResponse(new Response($message->contents, Response::HTTP_OK, [
                'Content-Type' => $message->contentType,
                'Pact-Message-Metadata' => \base64_encode($message->metadata),
            ]));
            $event->stopPropagation();
        }
    }
}
