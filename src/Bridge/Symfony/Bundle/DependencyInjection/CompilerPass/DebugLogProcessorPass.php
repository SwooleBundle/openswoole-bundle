<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\Bundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is an override for Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddDebugLogProcessorPass
 * which removes debug processor from loggers when Symfony is run from CLI.
 * Without debug log processor, there are no logs being shown in the symfony profiler. This override
 * replaces the original configurator for 'monolog.logger_prototype' with a custom one, which removes
 * the debug log processor only when being run from PHPDBG.
 * Since Symfony 6.4, use env variable `APP_RUNTIME_MODE: 'web=1'`, or parameter `kernel.runtime_mode.web: true`.
 */
final class DebugLogProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('profiler')) {
            return;
        }
        if (!$container->hasDefinition('monolog.logger_prototype')) {
            return;
        }
        if (!$container->hasDefinition('debug.log_processor')) {
            return;
        }

        $definition = $container->getDefinition('monolog.logger_prototype');
        $definition->setConfigurator([self::class, 'configureLogger']);
    }

    public static function configureLogger(object $logger): void
    {
        if (!method_exists($logger, 'removeDebugLogger')) {
            return;
        }

        if (!in_array(PHP_SAPI, ['cli', 'phpdbg'], true)) {
            return;
        }

        if (PHP_SAPI === 'cli') {
            foreach ($_SERVER['argv'] as $arg) { // phpcs:ignore
                if (mb_strpos((string) $arg, 'swoole:server:') !== false) {
                    return;
                }
            }
        }

        $logger->removeDebugLogger();
    }
}
