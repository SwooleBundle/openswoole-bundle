<?php

declare(strict_types=1);

namespace SwooleBundle\SwooleBundle\Tests\Fixtures\Symfony\TestBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\Routing\Attribute\Route;

final class SymfonyHttpController
{
    /**
     * @RouteAnnotation(
     *     methods={"GET"},
     *     path="/http/request/uri"
     * )
     * @see \SwooleBundle\SwooleBundle\Tests\Feature\SymfonyHttpRequestContainsRequestUriTest::testWhetherCurrentSymfonyHttpRequestContainsRequestUri()
     */
    #[Route(path: '/http/request/uri', methods: ['GET'])]
    public function getRequestUri(Request $currentRequest): JsonResponse
    {
        return new JsonResponse(['requestUri' => $currentRequest->getRequestUri()], 200);
    }

    /**
     * @RouteAnnotation(
     *     methods={"GET"},
     *     path="/http/request/streamed-uri"
     * )
     */
    #[Route(path: '/http/request/streamed-uri', methods: ['GET'])]
    public function getStreamedRequestUri(Request $currentRequest): StreamedResponse
    {
        $response = new StreamedResponse(static function () use ($currentRequest): void {
            $response = ['requestUri' => $currentRequest->getRequestUri()];
            echo json_encode($response);
        });
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
