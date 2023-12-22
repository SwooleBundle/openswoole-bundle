<?php

declare(strict_types=1);

namespace K911\Swoole\Bridge\Symfony\Event;

use Swoole\Server;
use Symfony\Contracts\EventDispatcher\Event;

final class WorkerErrorEvent extends Event
{
    public const NAME = 'swoole_bundle.worker.error';

    public function __construct(
        private readonly Server $server,
        private readonly int $workerId
    ) {
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getWorkerId(): int
    {
        return $this->workerId;
    }
}
