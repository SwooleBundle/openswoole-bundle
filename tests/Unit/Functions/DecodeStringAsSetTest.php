<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Unit\Functions;

use PHPUnit\Framework\TestCase;

use function SwooleBundle\SwooleBundle\decode_string_as_set;

class DecodeStringAsSetTest extends TestCase
{
    public static function decodedPairsProvider(): array
    {
        return [
            'normal set' => [
                'value1,value2,value3',
                ['value1', 'value2', 'value3'],
            ],
            'json set' => [
                "['value1','value2','value3']",
                ['value1', 'value2', 'value3'],
            ],
            'empty apostrophe set' => [
                "['''',''''',''''']",
                ['', '', ''],
            ],
            'apostrophe set' => [
                "['value1''','''value2'','''value3'']",
                ['value1', 'value2', 'value3'],
            ],
            'set from empty string' => [
                '',
                [],
            ],
            'empty set from null' => [
                null,
                [],
            ],
        ];
    }

    /**
     * @dataProvider decodedPairsProvider
     */
    public function testDecodeStringAsSet(?string $string, array $set): void
    {
        self::assertSame($set, decode_string_as_set($string));
    }
}
