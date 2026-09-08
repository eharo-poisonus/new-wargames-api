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
    private const string REFRESH_TOKEN_COOKIE = 'refresh_token';
    private const string SESSION_ID_COOKIE = 'session_id';

    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $response = $this->queryBus->ask(
            new RefreshTokenQuery(
                $request->cookies->get(self::SESSION_ID_COOKIE, ''),
                $request->cookies->get(self::REFRESH_TOKEN_COOKIE, '')
            )
        );

        $jsonResponse = new JsonResponse($response);

        $jsonResponse->headers->setCookie(
            Cookie::create(self::SESSION_ID_COOKIE)
                ->withValue($response->sessionId())
                ->withHttpOnly(true)
                ->withSecure(true)
                ->withSameSite('lax')
                ->withPath('/')
        );

        $jsonResponse->headers->setCookie(
            Cookie::create(self::REFRESH_TOKEN_COOKIE)
                ->withValue($response->refreshToken())
                ->withHttpOnly(true)
                ->withSecure(true)
                ->withSameSite('lax')
                ->withPath('/')
        );

        return $jsonResponse;
    }
}
