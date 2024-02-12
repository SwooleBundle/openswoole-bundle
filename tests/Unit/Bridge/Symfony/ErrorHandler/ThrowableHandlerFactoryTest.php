<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Bridge\Symfony\ErrorHandler;

use PHPUnit\Framework\TestCase;
use SwooleBundle\SwooleBundle\Bridge\Symfony\ErrorHandler\ThrowableHandlerFactory;

final class ThrowableHandlerFactoryTest extends TestCase
{
    public function testThrowableHandlerCreation(): void
    {
        $handler = ThrowableHandlerFactory::newThrowableHandler();
        $methodName = $handler->getName();

        self::assertTrue($methodName === 'handleThrowable' || $methodName === 'handleException');
    }
}
