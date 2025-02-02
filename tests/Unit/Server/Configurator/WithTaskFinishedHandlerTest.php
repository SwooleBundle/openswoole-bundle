<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Server\Configurator;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SwooleBundle\SwooleBundle\Server\Configurator\WithTaskFinishedHandler;
use SwooleBundle\SwooleBundle\Server\HttpServerConfiguration;
use SwooleBundle\SwooleBundle\Server\TaskHandler\NoOpTaskFinishedHandler;
use SwooleBundle\SwooleBundle\Tests\Unit\Server\IntMother;
use SwooleBundle\SwooleBundle\Tests\Unit\Server\SameClosureAssertion;
use SwooleBundle\SwooleBundle\Tests\Unit\Server\SwooleHttpServerMockFactory;

#[RunTestsInSeparateProcesses]
final class WithTaskFinishedHandlerTest extends TestCase
{
    use SameClosureAssertion;
    use ProphecyTrait;

    private NoOpTaskFinishedHandler $noOpTaskFinishedHandler;

    private WithTaskFinishedHandler $configurator;

    private HttpServerConfiguration|ObjectProphecy $configurationProphecy;

    protected function setUp(): void
    {
        $this->noOpTaskFinishedHandler = new NoOpTaskFinishedHandler();
        $this->configurationProphecy = $this->prophesize(HttpServerConfiguration::class);

        /** @var HttpServerConfiguration $configurationMock */
        $configurationMock = $this->configurationProphecy->reveal();

        $this->configurator = new WithTaskFinishedHandler($this->noOpTaskFinishedHandler, $configurationMock);
    }

    public function testConfigure(): void
    {
        $this->configurationProphecy->getTaskWorkerCount()
            ->willReturn(IntMother::randomPositive())
            ->shouldBeCalled();

        $swooleServerOnEventSpy = SwooleHttpServerMockFactory::make();

        $this->configurator->configure($swooleServerOnEventSpy);

        self::assertTrue($swooleServerOnEventSpy->registeredEvent());
        self::assertSame('finish', $swooleServerOnEventSpy->registeredEventPair()[0]);
        self::assertSameClosure(
            $this->noOpTaskFinishedHandler->handle(...),
            $swooleServerOnEventSpy->registeredEventPair()[1],
        );
    }

    public function testDoNotConfigureWhenNoTaskWorkers(): void
    {
        $this->configurationProphecy->getTaskWorkerCount()
            ->willReturn(0)
            ->shouldBeCalled();

        $swooleServerOnEventSpy = SwooleHttpServerMockFactory::make();

        $this->configurator->configure($swooleServerOnEventSpy);

        self::assertFalse($swooleServerOnEventSpy->registeredEvent());
    }
}
