<?php

namespace App\Shared\Infrastructure\Api;

use App\Shared\Domain\Bus\Command\CommandBus;

abstract class CommandController extends BaseController
{
    public function __construct(
        protected readonly CommandBus $commandBus
    ) {
    }
}
