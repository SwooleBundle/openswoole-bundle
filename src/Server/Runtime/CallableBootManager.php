<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Server\Runtime;

use Assert\Assertion;
use Assert\AssertionFailedException;

/**
 * Chain of services implementing BootableInterface.
 */
final class CallableBootManager implements Bootable
{
    /**
     * @param iterable<callable> $bootables
     */
    public function __construct(
        private readonly iterable $bootables,
        private bool $booted = false,
    ) {}

    /**
     * {@inheritDoc}
     *
     * Method MUST be called directly before Swoole server start.
     *
     * @throws AssertionFailedException When already booted
     */
    public function boot(array $runtimeConfiguration = []): void
    {
        Assertion::false($this->booted, 'Boot method has already been called. Cannot boot services multiple times.');
        $this->booted = true;

        /** @var callable $bootable */
        foreach ($this->bootables as $bootable) {
            $bootable($runtimeConfiguration);
        }
    }
}
