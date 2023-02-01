<?php

declare(strict_types=1);

namespace K911\Swoole\Bridge\Symfony\Container;

use ZEngine\Reflection\ReflectionClass;

final class ContainerModifier
{
    public static function modifyContainer(BlockingContainer $container): void
    {
        $reflContainer = new ReflectionClass($container);

        if (!$reflContainer->hasMethod('createProxy')) {
            return;
        }

        self::overrideCreateProxy($container, $reflContainer);
        self::overrideLoad($container, $reflContainer);
    }

    public static function overrideDoInExtension(string $containerDir, string $fileToLoad, string $class): void
    {
        require $containerDir.\DIRECTORY_SEPARATOR.$fileToLoad;

        $refl = new ReflectionClass($class);
        $reflDo = $refl->getMethod('do');
        $refl->addMethod('doOriginal', $reflDo->getClosure());

        $reflDo->redefine(function ($container, $lazyLoad = true) {
            $lockName = get_called_class().'::DO';
            $lock = self::$locking->acquire($lockName);

            try {
                $return = self::doOriginal($container, $lazyLoad);
            } finally {
                $lock->release();
            }

            return $return;
        });
    }

    private static function overrideCreateProxy(BlockingContainer $container, ReflectionClass $reflContainer): void
    {
        $createProxyRefl = $reflContainer->getMethod('createProxy');
        $reflContainer->addMethod('createProxyOriginal', $createProxyRefl->getClosure($container));
        $createProxyRefl->redefine(function ($class, \Closure $factory) {
            $lock = self::$locking->acquire($class);

            try {
                $return = $this->createProxyOriginal($class, $factory);
            } finally {
                $lock->release();
            }

            return $return;
        });
    }

    private static function overrideLoad(BlockingContainer $container, ReflectionClass $reflContainer): void
    {
        BlockingContainer::setBuildContainerNs($reflContainer->getNamespaceName());
        $loadRefl = $reflContainer->getMethod('load');
        $reflContainer->addMethod('loadOriginal', $loadRefl->getClosure($container));
        $loadRefl->redefine(function ($file, $lazyLoad = true) {
            $lock = self::$locking->acquire($file);

            try {
                $fileToLoad = $file;
                $class = self::$buildContainerNs.'\\'.$file;
                if ('.' === $file[-4]) {
                    $class = substr($class, 0, -4);
                } else {
                    $fileToLoad .= '.php';
                }

                if (!class_exists($class, false)) {
                    ContainerModifier::overrideDoInExtension($this->containerDir, $fileToLoad, $class);
                }

                $return = $this->loadOriginal($file, $lazyLoad);
            } finally {
                $lock->release();
            }

            return $return;
        });
    }
}
