<?php

declare(strict_types=1);

namespace K911\Swoole\Bridge\Symfony\Container;

use ZEngine\Reflection\ReflectionMethod;

final class ContainerSourceCodeExtractor
{
    private array $sourceCode;

    public function __construct(string $sourceCode)
    {
        $this->sourceCode = explode(PHP_EOL, $sourceCode);
    }

    public function getContainerInternalsForMethod(ReflectionMethod $method): array
    {
        $code = $this->getMethodCode($method);

        if (!preg_match(
            '/return \\$this->(?P<type>[a-z]+)\[\'(?P<key>[^\']+)\'\](\[\'(?P<key2>[^\']+)\'\])? \=/',
            $code,
            $matches
        )) {
            return [];
        }

        return $matches;
    }

    public function getMethodCode(ReflectionMethod $method): string
    {
        $startLine = $method->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
        $endLine = $method->getEndLine();
        $length = $endLine - $startLine;

        return implode(PHP_EOL, array_slice($this->sourceCode, $startLine, $length));
    }
}
