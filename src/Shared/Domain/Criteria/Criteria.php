<?php

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Groups\FiltersGroup;
use App\Shared\Domain\Criteria\Order\Order;

final class Criteria
{
    private function __construct(
        private array $filtersGroups,
        private Order $order,
        private ?int $offset,
        private ?int $limit
    ) {
    }

    public static function create(
        array $filtersGroups = [],
        ?Order $order = null,
        ?int $offset = null,
        ?int $limit = null
    ) {
        return new self(
            $filtersGroups,
            $order ?? Order::none(),
            $offset,
            $limit
        );
    }

    public function forCountTotal(): self
    {
        return new self(
            $this->filtersGroups,
            Order::none(),
            null,
            null
        );
    }

    public function addFiltersGroup(FiltersGroup $filtersGroup): void
    {
        $this->filtersGroups[] = $filtersGroup;
    }

    public function hasFilters(): bool
    {
        return !empty($this->filtersGroups);
    }

    public function hasOrder(): bool
    {
        return !$this->order->isNone();
    }

    public function filtersGroups(): array
    {
        return $this->filtersGroups;
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function serialize(): string
    {
        $query = '';

        foreach ($this->filtersGroups as $filterGroup) {
            $query .= $filterGroup->serialize();
        }

        return $query;
    }
}
