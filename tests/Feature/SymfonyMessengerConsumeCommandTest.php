<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Feature;

use SwooleBundle\SwooleBundle\Bridge\Symfony\Messenger\Exception\ReceiverNotAvailableException;
use SwooleBundle\SwooleBundle\Tests\Fixtures\Symfony\TestBundle\Test\ServerTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyMessengerConsumeCommandTest extends ServerTestCase
{
    protected function setUp(): void
    {
        $this->markTestSkippedIfXdebugEnabled();
        $this->deleteVarDirectory();
    }

    public function testConsumeMessagesFail(): void
    {
        $kernel = self::createKernel(['environment' => 'messenger']);
        $application = new Application($kernel);

        $command = $application->find('messenger:consume');
        $commandTester = new CommandTester($command);

        $this->expectException(ReceiverNotAvailableException::class);
        $this->expectExceptionMessage(
            'Swoole Server Task transport does not implement Receiver interface methods. '
            . 'Messages sent via Swoole Server Task transport are dispatched inside task worker processes.'
        );

        $commandTester->execute([
            'command' => $command->getName(),
            'receivers' => ['swoole'],
        ]);
    }
}
