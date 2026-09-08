<?php

namespace App\Authentication\RefreshTokens\Infrastructure\Api;

use App\Authentication\RefreshTokens\Application\RefreshRefreshToken\RefreshTokenQuery;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Infrastructure\Api\BaseController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostRefreshTokenController extends BaseController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $response = $this->queryBus->ask(
            new RefreshTokenQuery(
                $request->cookies->get('session_id', ''),
                $request->cookies->get('refresh_token', '')
            )
        );

        $jsonResponse = new JsonResponse($response);

        $jsonResponse->headers->setCookie(
            Cookie::create('session_id')
                ->withValue($response->sessionId())
                ->withHttpOnly(true)
                ->withSecure(true)
                ->withSameSite('lax')
                ->withPath('/')
        );

        $jsonResponse->headers->setCookie(
            Cookie::create('refresh_token')
                ->withValue($response->refreshToken())
                ->withHttpOnly(true)
                ->withSecure(true)
                ->withSameSite('lax')
                ->withPath('/')
        );

        return $jsonResponse;
    }
}
