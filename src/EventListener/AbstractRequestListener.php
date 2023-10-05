<?php

namespace Tienvx\Bundle\PactProviderBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tienvx\Bundle\PactProviderBundle\Controller\ControllerInterface;

abstract class AbstractRequestListener
{
    public function __construct(
        private ControllerInterface $controller,
        private string $url,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        if ($this->url === $request->getPathInfo() && Request::METHOD_POST === $request->getMethod()) {
            $response = $this->controller->handle($request);
            if ($response) {
                $event->setResponse($response);
            }
        }
    }
}
