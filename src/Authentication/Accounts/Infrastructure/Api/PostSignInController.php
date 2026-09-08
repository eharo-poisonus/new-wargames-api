<?php

namespace App\Authentication\Accounts\Infrastructure\Api;

use App\Authentication\Accounts\Application\SignIn\SignInQuery;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Infrastructure\Api\BaseController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostSignInController extends BaseController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $requestData = json_decode($request->getContent());

        $response = $this->queryBus->ask(
            new SignInQuery(
                $requestData->email,
                $requestData->password,
                $request->headers->get('User-Agent'),
                $request->getClientIp()
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
