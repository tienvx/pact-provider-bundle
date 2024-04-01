<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Tienvx\Bundle\PactProviderBundle\Enum\Action;
use Tienvx\Bundle\PactProviderBundle\Exception\LogicException;
use Tienvx\Bundle\PactProviderBundle\Exception\NoHandlerForStateException;
use Tienvx\Bundle\PactProviderBundle\Model\StateValues;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManager;
use Tienvx\Bundle\PactProviderBundle\Service\StateHandlerManagerInterface;
use Tienvx\Bundle\PactProviderBundle\StateHandler\SetUpInterface;
use Tienvx\Bundle\PactProviderBundle\StateHandler\TearDownInterface;

class StateHandlerManagerTest extends TestCase
{
    private StateHandlerManagerInterface $stateHandlerManager;
    private ServiceLocator|MockObject $locator;
    private array $params = ['key' => 'value'];

    protected function setUp(): void
    {
        $this->locator = $this->createMock(ServiceLocator::class);
        $this->stateHandlerManager = new StateHandlerManager($this->locator);
    }

    #[TestWith([Action::SETUP])]
    #[TestWith([Action::TEARDOWN])]
    public function testNoHandler(Action $action): void
    {
        $state = 'no handler';
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($state)
            ->willReturn(false);
        $this->expectException(NoHandlerForStateException::class);
        $this->expectExceptionMessage(sprintf("No handler for state '%s'.", $state));
        $this->stateHandlerManager->handle($state, $action, $this->params);
    }

    public function testSetupInvalidHandler(): void
    {
        $state = 'setup invalid handler';
        $action = Action::SETUP;
        $handler = function () {};
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($state)
            ->willReturn(true);
        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with($state)
            ->willReturn($handler);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf('Handler "Closure" must implement "%s".', SetUpInterface::class));
        $this->stateHandlerManager->handle($state, $action, $this->params);
    }

    public function testTeardownInvalidHandler(): void
    {
        $state = 'teardown invalid handler';
        $action = Action::TEARDOWN;
        $handler = function () {};
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($state)
            ->willReturn(true);
        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with($state)
            ->willReturn($handler);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf('Handler "Closure" must implement "%s".', TearDownInterface::class));
        $this->stateHandlerManager->handle($state, $action, $this->params);
    }

    #[TestWith([null])]
    #[TestWith([new StateValues(['key' => 'value'])])]
    public function testSetup(?StateValues $stateValues): void
    {
        $state = 'setup state';
        $action = Action::SETUP;
        $handler = $this->createMock(SetUpInterface::class);
        $handler
            ->expects($this->once())
            ->method('setUp')
            ->with($this->params)
            ->willReturn($stateValues);
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($state)
            ->willReturn(true);
        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with($state)
            ->willReturn($handler);
        $this->assertSame($stateValues, $this->stateHandlerManager->handle($state, $action, $this->params));
    }

    public function testTeardown(): void
    {
        $state = 'teardown state';
        $action = Action::TEARDOWN;
        $handler = $this->createMock(TearDownInterface::class);
        $handler
            ->expects($this->once())
            ->method('tearDown')
            ->with($this->params);
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($state)
            ->willReturn(true);
        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with($state)
            ->willReturn($handler);
        $this->assertNull($this->stateHandlerManager->handle($state, $action, $this->params));
    }
}
