<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\Messenger;

use Assert\Assertion;
use Swoole\Server;
use SwooleBundle\SwooleBundle\Server\TaskHandler\TaskHandlerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class SwooleServerTaskTransportHandler implements TaskHandlerInterface
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ?TaskHandlerInterface $decorated = null
    ) {
    }

    public function handle(Server $server, Server\Task $task): void
    {
        Assertion::isInstanceOf($task->data, Envelope::class);
        /* @var $data Envelope */
        $data = $task->data;
        $this->bus->dispatch($data);

        if ($this->decorated instanceof TaskHandlerInterface) {
            $this->decorated->handle($server, $task);
        }
    }
}
