<?php

namespace App\Shared\Infrastructure\Symfony\Middlewares;

use App\Shared\Domain\Exception\TooManyAttemptsException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

final readonly class HttpRateLimitMiddleware
{
    private const array LIMITED_ROUTES = [
        'accounts.sign-in' => 'signIn',
        'accounts.create' => 'signUp',
        'accounts.forgot-password' => 'passwordReset',
        'refresh-tokens.refresh' => 'refreshToken'
    ];

    public function __construct(
        private RateLimiterFactoryInterface $signInLimiter,
        private RateLimiterFactoryInterface $signUpLimiter,
        private RateLimiterFactoryInterface $passwordResetLimiter,
        private RateLimiterFactoryInterface $refreshTokenLimiter
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $which = self::LIMITED_ROUTES[$request->attributes->get('_route')] ?? null;

        if ($which === null) {
            return;
        }

        $limiter = match ($which) {
            'signIn' => $this->signInLimiter,
            'signUp' => $this->signUpLimiter,
            'refreshToken' => $this->refreshTokenLimiter,
            default => $this->passwordResetLimiter
        };

        if (!$limiter->create($request->getClientIp() ?? 'unknown')->consume()->isAccepted()) {
            throw new TooManyAttemptsException();
        }
    }
}
