<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Bridge\Log;

use PHPUnit\Framework\TestCase;
use SwooleBundle\SwooleBundle\Bridge\Log\StrftimeToICUFormatMap;

class StrftimeToICUFormatMapTest extends TestCase
{
    public function testPatternsUsedInAccessLogFormatter(): void
    {
        $this->assertSame(
            'dd/MMM/y:HH:mm:ss xx',
            StrftimeToICUFormatMap::mapStrftimeToICU('%d/%b/%Y:%H:%M:%S %z', new \DateTimeImmutable('now'))
        );
    }

    public function testDoesNotReplaceICUFormats(): void
    {
        $this->assertSame(
            'dd/MMM/y:HH:mm:ss xx',
            StrftimeToICUFormatMap::mapStrftimeToICU('dd/MMM/y:HH:mm:ss xx', new \DateTimeImmutable('now'))
        );
    }

    public static function unsupportedFormats(): array
    {
        return [
            '%c' => ['%c'],
            '%x' => ['%x'],
            '%X' => ['%X'],
        ];
    }

    /**
     * @dataProvider unsupportedFormats
     */
    public function testRaisesExceptionForUnsupportedFormats(string $format): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('unsupported');
        StrftimeToICUFormatMap::mapStrftimeToICU($format, new \DateTimeImmutable('now'));
    }
}
