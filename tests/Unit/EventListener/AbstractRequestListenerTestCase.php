<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tienvx\Bundle\PactProviderBundle\Controller\ControllerInterface;
use Tienvx\Bundle\PactProviderBundle\EventListener\AbstractRequestListener;

abstract class AbstractRequestListenerTestCase extends TestCase
{
    private AbstractRequestListener $listener;
    protected ControllerInterface|MockObject $controller;
    private RequestEvent|MockObject $event;
    private Request|MockObject $request;
    private Response|MockObject $response;
    protected string $url = '/path-info';
    protected string $method = 'POST';

    protected function setUp(): void
    {
        $this->controller = $this->createMock(ControllerInterface::class);
        $this->event = $this->createMock(RequestEvent::class);
        $this->request = $this->createMock(Request::class);
        $this->response = $this->createMock(Response::class);
        $this->listener = $this->createListener();
    }

    abstract protected function createListener(): AbstractRequestListener;

    public function testNotMainRequest(): void
    {
        $this->event
            ->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(false);
        $this->event
            ->expects($this->never())
            ->method('getRequest');
        call_user_func($this->listener, $this->event);
    }

    #[TestWith(['/some-path', 'GET'])]
    #[TestWith(['/path-info', 'GET'])]
    #[TestWith(['/other-path', 'POST'])]
    public function testNotSupportedRequest(string $url, string $method): void
    {
        $this->event
            ->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(true);
        $this->event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->request
            ->expects($this->once())
            ->method('getPathInfo')
            ->willReturn($url);
        $this->request
            ->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);
        $this->controller
            ->expects($this->never())
            ->method('handle');
        call_user_func($this->listener, $this->event);
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function testHandle(bool $hasResponse): void
    {
        $response = $hasResponse ? $this->response : null;
        $this->event
            ->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(true);
        $this->event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->request
            ->expects($this->once())
            ->method('getPathInfo')
            ->willReturn($this->url);
        $this->request
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn($this->method);
        $this->controller
            ->expects($this->once())
            ->method('handle')
            ->with($this->request)
            ->willReturn($response);
        if ($response) {
            $this->event
                ->expects($this->once())
                ->method('setResponse')
                ->with($response);
        } else {
            $this->event
                ->expects($this->never())
                ->method('setResponse');
        }
        call_user_func($this->listener, $this->event);
    }
}
