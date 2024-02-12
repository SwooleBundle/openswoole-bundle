<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Coroutine;

use PHPUnit\Framework\TestCase;
use SwooleBundle\SwooleBundle\Coroutine\CoroutinePool;

// phpcs:disable SlevomatCodingStandard.PHP.DisallowReference.DisallowedInheritingVariableByReference
final class CoroutinePoolTest extends TestCase
{
    public function testCoroutinePoolWorks(): void
    {
        $value = null;
        $expected = 1;

        $pool = CoroutinePool::fromCoroutines(
            static function () use (&$value, $expected): void {
                $value = $expected;
            },
        );
        $pool->run();

        self::assertSame($expected, $value);
    }

    public function testCoroutinePoolWithManyCoroutinesWorks(): void
    {
        $value1 = null;
        $expected1 = 1;

        $value2 = null;
        $expected2 = 2;

        $value3 = null;
        $expected3 = 3;

        $pool = CoroutinePool::fromCoroutines(
            static function () use (&$value1, $expected1): void {
                $value1 = $expected1;
            },
            static function () use (&$value2, $expected2): void {
                $value2 = $expected2;
            },
            static function () use (&$value3, $expected3): void {
                $value3 = $expected3;
            }
        );
        $pool->run();

        self::assertSame($expected1, $value1);
        self::assertSame($expected2, $value2);
        self::assertSame($expected3, $value3);
    }

    public function testCoroutinePoolInCoroutinePoolWorks(): void
    {
        $value1 = null;
        $expected1 = 1;

        $value2 = null;
        $expected2 = 2;

        $pool1 = CoroutinePool::fromCoroutines(
            static function () use (&$value1, &$value2, $expected1, $expected2): void {
                $pool2 = CoroutinePool::fromCoroutines(
                    static function () use (&$value2, $expected2): void {
                        $value2 = $expected2;
                    },
                );
                $pool2->run();
                $value1 = $expected1;
            },
        );
        $pool1->run();

        self::assertSame($expected1, $value1);
        self::assertSame($expected2, $value2);
    }
}
