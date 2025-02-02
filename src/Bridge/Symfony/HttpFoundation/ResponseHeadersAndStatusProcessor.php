<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Bridge\Symfony\HttpFoundation;

use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

final readonly class ResponseHeadersAndStatusProcessor implements ResponseProcessor
{
    public function __construct(private ResponseProcessor $decorated) {}

    public function process(HttpFoundationResponse $httpFoundationResponse, SwooleResponse $swooleResponse): void
    {
        foreach ($httpFoundationResponse->headers->allPreserveCaseWithoutCookies() as $name => $values) {
            $swooleResponse->header($name, implode(', ', $values));
        }

        foreach ($httpFoundationResponse->headers->getCookies() as $cookie) {
            $swooleResponse->cookie(
                $cookie->getName(),
                $cookie->getValue() ?? '',
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain() ?? '',
                $cookie->isSecure(),
                $cookie->isHttpOnly(),
                $cookie->getSameSite() ?? ''
            );
        }

        $swooleResponse->status($httpFoundationResponse->getStatusCode());

        $this->decorated->process($httpFoundationResponse, $swooleResponse);
    }
}
