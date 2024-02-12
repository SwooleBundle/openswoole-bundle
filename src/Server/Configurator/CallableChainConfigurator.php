<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Server\Configurator;

use Swoole\Http\Server;

final class CallableChainConfigurator implements Configurator
{
    /**
     * @param iterable<callable> $configurators
     */
    public function __construct(
        private readonly iterable $configurators,
    ) {}

    public function configure(Server $server): void
    {
        /** @var callable $configurator */
        foreach ($this->configurators as $configurator) {
            $configurator($server);
        }
    }
}
