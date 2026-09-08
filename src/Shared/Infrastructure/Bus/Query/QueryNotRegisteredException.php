<?php

namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Domain\Bus\Query\Query;
use RuntimeException;

final class QueryNotRegisteredException extends RuntimeException
{
    public function __construct(Query $query)
    {
        $queryClass = $query::class;
        parent::__construct("The query <$queryClass> has no associated query handler");
    }
}
