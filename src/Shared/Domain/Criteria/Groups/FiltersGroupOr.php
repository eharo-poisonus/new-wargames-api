<?php

namespace App\Shared\Domain\Criteria\Groups;

class FiltersGroupOr extends FiltersGroup
{
    public function __construct(
        array $filters,
        array $filtersGroups,
        string $logicalOperatorBetweenFiltersInGroup,
        ?string $logicalOperatorWithPreviousGroup
    ) {
        $this->filters = $filters;
        $this->filtersGroups = $filtersGroups;
        $this->logicalOperatorBetweenFiltersInGroup = $logicalOperatorBetweenFiltersInGroup;
        $this->logicalOperatorWithPreviousGroup = $logicalOperatorWithPreviousGroup;
    }

    public static function fromValues(
        array $filters,
        array $filtersGroups = [],
        ?string $logicalOperatorWithPreviousGroup = null
    ): self {
        return new self(
            $filters,
            $filtersGroups,
            LogicalOperator::OR->value,
            $logicalOperatorWithPreviousGroup
        );
    }
}
