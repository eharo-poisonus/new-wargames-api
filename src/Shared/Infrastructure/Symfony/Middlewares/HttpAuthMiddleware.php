<?php

namespace App\Shared\Infrastructure\Symfony\Middlewares;

use App\Shared\Domain\Auth;
use App\Shared\Domain\Bus\Command\CommandBus;
use Exception;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class HttpAuthMiddleware
{
    private const string ROUTE_AUTHORIZATION_TYPE = 'AUTH';
    private const string BEARER_HEADER = 'authorization';

    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $authType = Auth::from(
            $event->getRequest()->attributes->get(self::ROUTE_AUTHORIZATION_TYPE, Auth::JWT->value)
        );

        switch ($authType) {
            case Auth::FREE:
                break;
            case Auth::JWT:
                $this->authenticateRequest($event);
                break;
        }
    }

    private function authenticateRequest(RequestEvent $event): void
    {

    }

    private function retrieveToken(RequestEvent $event): string
    {
        return str_replace('Bearer ', '', $event->getRequest()->headers->get(self::BEARER_HEADER) ?? '');
    }

    private function guardAuthenticationHeaderExist(?string $token): void
    {
        if ($token === null) {
            throw new Exception('Error Auth');
        }
    }
}
