<?php

namespace App\Shared\Infrastructure\Api;

use App\Shared\Domain\Bus\Query\QueryBus;

abstract class QueryController extends BaseController
{
    public function __construct(
        protected readonly QueryBus $queryBus
    ) {
    }
}
