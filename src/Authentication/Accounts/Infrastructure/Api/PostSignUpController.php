<?php

namespace App\Authentication\Accounts\Infrastructure\Api;

use App\Authentication\Accounts\Application\CreateAccount\CreateAccountCommand;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Infrastructure\Api\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostSignUpController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent());

        $this->commandBus->dispatch(
            new CreateAccountCommand(
                $data->id,
                $data->type,
                $data->username,
                $data->email,
                $data->password,
                $data->referred_by,
                $data->email_marketing_accepted,
                $data->terms_accepted,
                $data->terms_version
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }
}
