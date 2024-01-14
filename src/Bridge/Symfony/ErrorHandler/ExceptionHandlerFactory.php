<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\ErrorHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ExceptionHandlerFactory
{
    public function __construct(
        private readonly HttpKernelInterface $kernel,
        private readonly \ReflectionMethod $throwableHandler
    ) {
    }

    public function newExceptionHandler(Request $request): ResponseDelayingExceptionHandler
    {
        return new ResponseDelayingExceptionHandler(
            $this->kernel,
            $request,
            $this->throwableHandler
        );
    }
}
