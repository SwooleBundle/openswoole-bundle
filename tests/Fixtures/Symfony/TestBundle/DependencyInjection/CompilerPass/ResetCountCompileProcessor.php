<?php

declare(strict_types=1);

namespace K911\Swoole\Tests\Fixtures\Symfony\TestBundle\DependencyInjection\CompilerPass;

use K911\Swoole\Bridge\Symfony\Bundle\DependencyInjection\CompilerPass\StatefulServices\CompileProcessor;
use K911\Swoole\Bridge\Symfony\Bundle\DependencyInjection\CompilerPass\StatefulServices\Proxifier;
use K911\Swoole\Tests\Fixtures\Symfony\TestBundle\Controller\DoctrineController;
use K911\Swoole\Tests\Fixtures\Symfony\TestBundle\Resetter\CountingResetter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ResetCountCompileProcessor implements CompileProcessor
{
    public function process(ContainerBuilder $container, Proxifier $proxifier): void
    {
        $this->decorateResetter($container, 'swoole_bundle.coroutines_support.doctrine.connection_resetter.default');
        $this->decorateResetter($container, 'inmemory_repository_resetter');
    }

    private function decorateResetter(ContainerBuilder $container, string $resetterId): void
    {
        $formerResetterDef = $container->findDefinition($resetterId);
        $newId = $resetterId.'.inner';
        $container->setDefinition($newId, $formerResetterDef);
        $counterDef = new Definition();
        $counterDef->setClass(CountingResetter::class);
        $counterDef->setArgument(0, new Reference($newId));
        $container->setDefinition($resetterId, $counterDef);

        $controllerDef = $container->findDefinition(DoctrineController::class);
        $resetters = $controllerDef->getArgument(1);
        $resetters[$resetterId] = new Reference($resetterId);
        $controllerDef->setArgument(1, $resetters);
    }
}
