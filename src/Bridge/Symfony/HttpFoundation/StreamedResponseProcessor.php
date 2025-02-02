<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\HttpFoundation;

use Assert\Assertion;
use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class StreamedResponseProcessor implements ResponseProcessor
{
    public function __construct(private int $bufferOutputSize = 8192) {}

    public function process(HttpFoundationResponse $httpFoundationResponse, SwooleResponse $swooleResponse): void
    {
        Assertion::isInstanceOf($httpFoundationResponse, StreamedResponse::class);

        ob_start(static function (string $payload) use ($swooleResponse) {
            if ($payload !== '') {
                $swooleResponse->write($payload);
            }

            return '';
        }, $this->bufferOutputSize);
        $httpFoundationResponse->sendContent();
        ob_end_clean();
        $swooleResponse->end();
    }
}
