<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Server\WorkerHandler;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use SwooleBundle\SwooleBundle\Server\WorkerHandler\HMRWorkerStartHandler;
use SwooleBundle\SwooleBundle\Tests\Unit\Server\IntMother;
use SwooleBundle\SwooleBundle\Tests\Unit\Server\Runtime\HMR\HMRSpy;
use SwooleBundle\SwooleBundle\Tests\Unit\Server\SwooleServerMockFactory;
use SwooleBundle\SwooleBundle\Tests\Unit\Server\SwooleSpy;

#[RunTestsInSeparateProcesses]
final class HMRWorkerStartHandlerTest extends TestCase
{
    private HMRSpy $hmrSpy;

    private SwooleSpy $swooleFacade;

    private HMRWorkerStartHandler $hmrWorkerStartHandler;

    protected function setUp(): void
    {
        $this->hmrSpy = new HMRSpy();
        $this->swooleFacade = new SwooleSpy();
        $this->hmrWorkerStartHandler = new HMRWorkerStartHandler($this->hmrSpy, $this->swooleFacade, 2000);
    }

    public function testTaskWorkerNotRegisterTick(): void
    {
        $serverMock = SwooleServerMockFactory::make(true);

        $this->hmrWorkerStartHandler->handle($serverMock, IntMother::random());

        self::assertFalse($this->swooleFacade->registeredTick());
    }

    public function testWorkerRegisterTick(): void
    {
        $serverMock = SwooleServerMockFactory::make();

        $this->hmrWorkerStartHandler->handle($serverMock, IntMother::random());

        self::assertTrue($this->swooleFacade->registeredTick());
        self::assertNotEmpty($this->swooleFacade->registeredTickTuple());
        self::assertSame(2000, $this->swooleFacade->registeredTickTuple()[0]);
        $this->assertCallbackTriggersTick($this->swooleFacade->registeredTickTuple()[1]);
    }

    private function assertCallbackTriggersTick(callable $callback): void
    {
        $callback();
        self::assertTrue($this->hmrSpy->ticked());
    }
}
