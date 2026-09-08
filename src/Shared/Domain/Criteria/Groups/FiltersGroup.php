<?php

namespace App\Shared\Domain\Criteria\Groups;

use App\Shared\Domain\Criteria\Filters\Filter;

abstract class FiltersGroup
{
    protected ?string $logicalOperatorWithPreviousGroup = null;
    protected string $logicalOperatorBetweenFiltersInGroup = LogicalOperator::AND->value;
    protected array $filters = [];
    protected array $filtersGroups = [];

    abstract public static function fromValues(
        array $filters,
        array $filtersGroups = [],
        ?string $logicalOperatorWithPreviousGroup = null
    ): self;

    public function filters(): array
    {
        return $this->filters;
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters[] = $filter;
    }

    public function filtersGroups(): array
    {
        return $this->filtersGroups;
    }

    public function addFiltersGroup(FiltersGroup $filtersGroup): void
    {
        $this->filtersGroups[] = $filtersGroup;
    }

    public function logicalOperatorWithPreviousGroup(): ?string
    {
        return $this->logicalOperatorWithPreviousGroup;
    }

    public function logicalOperatorBetweenFiltersInGroup(): string
    {
        return $this->logicalOperatorBetweenFiltersInGroup;
    }

    public function serialize(): string
    {
        $query = $this->logicalOperatorWithPreviousGroup ? ' ' . $this->logicalOperatorBetweenFiltersInGroup . ' (' : '(';

        $serializedFields = array_map(
            fn ($filter) => $filter->serialize(),
            $this->filters
        );

        $serializedGroups = array_map(
            fn ($group) => $group->serialize(),
            $this->filtersGroups
        );

        $serializedItems = array_merge($serializedFields, $serializedGroups);

        $query .= implode(' ' . $this->logicalOperatorBetweenFiltersInGroup . ' ', $serializedItems);

        return $query . ')';
    }
}
