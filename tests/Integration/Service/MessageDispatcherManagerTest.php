<?php

namespace Tienvx\Bundle\PactProviderBundle\Tests\Integration\Service;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tienvx\Bundle\PactProviderBundle\Exception\NoDispatcherForMessageException;
use Tienvx\Bundle\PactProviderBundle\Model\Message;
use Tienvx\Bundle\PactProviderBundle\Service\MessageDispatcherManagerInterface;

class MessageDispatcherManagerTest extends KernelTestCase
{
    #[TestWith(['no message', null])]
    #[TestWith(['has message', new Message('message content', 'text/plain', '{"key":"value","contentType":"text\/plain"}')])]
    public function testHandle(string $description, ?Message $message): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $manager = $container->get(MessageDispatcherManagerInterface::class);
        $this->assertEquals($message, $manager->dispatch($description));
    }

    public function testMissingDispatcher(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $manager = $container->get(MessageDispatcherManagerInterface::class);

        $description = 'no dispatcher';

        $this->expectException(NoDispatcherForMessageException::class);
        $this->expectExceptionMessage(sprintf("No dispatcher for description '%s'.", $description));

        $manager->dispatch($description);
    }
}
