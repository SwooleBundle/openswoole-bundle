<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Common\Adapter;

use PHPUnit\Framework\TestCase;
use SwooleBundle\SwooleBundle\Common\Adapter\Swoole;
use SwooleBundle\SwooleBundle\Common\Adapter\SwooleFactory;
use SwooleBundle\SwooleBundle\Common\Adapter\SystemSwooleFactory;
use SwooleBundle\SwooleBundle\Common\System\Extension;
use SwooleBundle\SwooleBundle\Common\System\System;

class SystemSwooleFactoryTest extends TestCase
{
    /**
     * @dataProvider extensions
     */
    public function testNewInstanceCreation(string $extension): void
    {
        if (!\extension_loaded($extension)) {
            self::markTestSkipped(\sprintf('Extension %s is not loaded.', $extension));
        }

        $swooleFactory = $this->createMock(SwooleFactory::class);
        $openSwooleFactory = $this->createMock(SwooleFactory::class);

        $expectingFactory = Extension::SWOOLE === $extension ? $swooleFactory : $openSwooleFactory;
        $expectingFactory->expects($this->once())
            ->method('newInstance')
            ->willReturn($this->createMock(Swoole::class))
        ;

        $factory = new SystemSwooleFactory(
            System::create(),
            new \ArrayIterator([
                Extension::SWOOLE => $swooleFactory,
                Extension::OPENSWOOLE => $openSwooleFactory,
            ])
        );

        $factory->newInstance();
    }

    public function testNewInstanceCreationFails(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Adapter factory for extension "(swoole|openswoole)" not found\./');

        $factory = new SystemSwooleFactory(
            System::create(),
            new \ArrayIterator([])
        );

        $factory->newInstance();
    }

    public static function extensions(): array
    {
        return [
            [Extension::SWOOLE],
            [Extension::OPENSWOOLE],
        ];
    }
}
