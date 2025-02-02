<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Component\Locking\FirstTimeOnly;

use SwooleBundle\SwooleBundle\Component\Locking\MutexFactory;

final readonly class FirstTimeOnlyMutexFactory implements MutexFactory
{
    public function __construct(private MutexFactory $wrapped) {}

    public function newMutex(): FirstTimeOnlyMutex
    {
        return new FirstTimeOnlyMutex($this->wrapped->newMutex());
    }
}
