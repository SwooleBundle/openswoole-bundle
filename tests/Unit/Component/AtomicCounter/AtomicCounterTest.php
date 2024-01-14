<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Component\AtomicCounter;

use PHPUnit\Framework\TestCase;
use SwooleBundle\SwooleBundle\Component\AtomicCounter;

class AtomicCounterTest extends TestCase
{
    public function testConstructFromZero(): void
    {
        $counter = AtomicCounter::fromZero();
        self::assertSame(0, $counter->get());
    }

    public function testIncrement(): void
    {
        $atomicSpy = new AtomicSpy();
        self::assertFalse($atomicSpy->incremented);

        $counter = new AtomicCounter($atomicSpy);
        $counter->increment();

        self::assertTrue($atomicSpy->incremented);
    }

    public function testGet(): void
    {
        $count = 10;
        $counter = new AtomicCounter(new AtomicStub($count));

        self::assertSame($count, $counter->get());
    }
}
