<?php

namespace App\Shared\Infrastructure\Symfony\Middlewares;

use App\Shared\Domain\Auth;
use App\Shared\Domain\Bus\Command\CommandBus;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class HttpRolesMiddleware
{
    private const string BEARER_HEADER = 'authorization';
    private const string REQUIRED_ROLES = 'ROLES';
    private const string ROUTE_AUTHORIZATION_TYPE = 'AUTH';
    private const array DEFAULT_ROLES = ['ROLE_USER'];

    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->shouldProcessRequest($event)) {
            return;
        }

        $token = $this->retrieveToken($event);

        $typeOfAuthForRequest = Auth::from(
            value: $event->getRequest()->attributes->get(self::ROUTE_AUTHORIZATION_TYPE, Auth::JWT->value)
        );

        if ($typeOfAuthForRequest === Auth::FREE) {
            return;
        }

        $roles = $event->getRequest()->attributes->get(self::REQUIRED_ROLES, self::DEFAULT_ROLES);

        //$this->commandBus->dispatch(
        //    new CheckRoleCommand(
        //        $token,
        //        $roles
        //    )
        //);
    }

    private function shouldProcessRequest(RequestEvent $event): bool
    {
        if (!$event->isMainRequest()) {
            return false;
        }

        $authType = Auth::from(
            $event->getRequest()->attributes->get(self::ROUTE_AUTHORIZATION_TYPE, Auth::JWT->value)
        );

        return $authType !== Auth::FREE;
    }

    private function retrieveToken(RequestEvent $event): string
    {
        return preg_replace('/^Bearer\s+/i', '', $event->getRequest()->headers->get(self::BEARER_HEADER) ?? '');
    }
}
