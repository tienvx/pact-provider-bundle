<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Tienvx\Bundle\PactProviderBundle\Exception\LogicException;
use Tienvx\Bundle\PactProviderBundle\Exception\NoDispatcherForMessageException;
use Tienvx\Bundle\PactProviderBundle\MessageDispatcher\DispatcherInterface;
use Tienvx\Bundle\PactProviderBundle\Model\Message;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManager;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManagerInterface;

class MessageDispatcherManagerTest extends TestCase
{
    private MessageDispatcherManagerInterface $messageDispatcherManager;
    private ServiceLocator|MockObject $locator;
    private array $params = ['key' => 'value'];

    protected function setUp(): void
    {
        $this->locator = $this->createMock(ServiceLocator::class);
        $this->messageDispatcherManager = new MessageDispatcherManager($this->locator);
    }

    public function testNoHandler(): void
    {
        $description = 'no dispatcher';
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($description)
            ->willReturn(false);
        $this->expectException(NoDispatcherForMessageException::class);
        $this->expectExceptionMessage(sprintf("No dispatcher for description '%s'.", $description));
        $this->messageDispatcherManager->dispatch($description);
    }

    public function testSetupInvalidDispatcher(): void
    {
        $description = 'invalid dispatcher';
        $handler = function () {};
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($description)
            ->willReturn(true);
        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with($description)
            ->willReturn($handler);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf('Dispatcher "Closure" must implement "%s".', DispatcherInterface::class));
        $this->messageDispatcherManager->dispatch($description);
    }

    #[TestWith([null])]
    #[TestWith([new Message('contents', 'type/subtype', 'extra info')])]
    public function testDispatch(?Message $message): void
    {
        $description = 'message event';
        $dispatcher = $this->createMock(DispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($message);
        $this->locator
            ->expects($this->once())
            ->method('has')
            ->with($description)
            ->willReturn(true);
        $this->locator
            ->expects($this->once())
            ->method('get')
            ->with($description)
            ->willReturn($dispatcher);
        $this->assertSame($message, $this->messageDispatcherManager->dispatch($description));
    }
}
