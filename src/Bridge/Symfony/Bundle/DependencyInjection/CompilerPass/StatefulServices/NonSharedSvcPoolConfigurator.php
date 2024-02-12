<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\Bundle\DependencyInjection\CompilerPass\StatefulServices;

use SwooleBundle\SwooleBundle\Bridge\Symfony\Container\ServicePool\BaseServicePool;
use SwooleBundle\SwooleBundle\Bridge\Symfony\Container\ServicePool\ServicePoolContainer;

final class NonSharedSvcPoolConfigurator
{
    public function __construct(private readonly ServicePoolContainer $container) {}

    /**
     * @param BaseServicePool<object> $servicePool
     */
    public function configure(BaseServicePool $servicePool): void
    {
        $this->container->addPool($servicePool);
    }
}
