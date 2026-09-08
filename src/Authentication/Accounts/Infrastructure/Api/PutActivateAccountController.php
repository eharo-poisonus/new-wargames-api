<?php

namespace App\Authentication\Accounts\Infrastructure\Api;

use App\Authentication\Accounts\Application\ActivateAccount\ActivateAccountCommand;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Infrastructure\Api\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PutActivateAccountController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $this->commandBus->dispatch(
            new ActivateAccountCommand(
                $request->attributes->get('account_id'),
                $request->attributes->get('token')
            )
        );

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
