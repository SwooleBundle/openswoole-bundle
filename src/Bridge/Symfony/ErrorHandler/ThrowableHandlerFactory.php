<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\ErrorHandler;

use Symfony\Component\HttpKernel\HttpKernel;

final class ThrowableHandlerFactory
{
    public static function newThrowableHandler(): \ReflectionMethod
    {
        $kernelReflection = new \ReflectionClass(HttpKernel::class);
        $method = $kernelReflection->getMethod('handleThrowable');

        return $method;
    }
}
