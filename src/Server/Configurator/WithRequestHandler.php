<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Server\Configurator;

use Swoole\Http\Server;
use SwooleBundle\SwooleBundle\Server\RequestHandler\RequestHandler;

final class WithRequestHandler implements Configurator
{
    public function __construct(private readonly RequestHandler $requestHandler) {}

    public function configure(Server $server): void
    {
        $server->on('request', [$this->requestHandler, 'handle']);
    }
}
