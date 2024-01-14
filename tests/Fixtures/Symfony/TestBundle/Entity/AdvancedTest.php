<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Fixtures\Symfony\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'advanced_test')]
#[ORM\Entity]
class AdvancedTest
{
    #[ORM\Column(type: 'integer')]
    private int $counter = 0;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'guid')]
        private UuidInterface $uuid
    ) {
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function increment(): int
    {
        return ++$this->counter;
    }
}
