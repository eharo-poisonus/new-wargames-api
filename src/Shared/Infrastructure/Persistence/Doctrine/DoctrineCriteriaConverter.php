<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters\Filter;
use App\Shared\Domain\Criteria\Filters\FilterFieldsMap;
use App\Shared\Domain\Criteria\Filters\FilterOperator;
use App\Shared\Domain\Criteria\Order\OrderType;
use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\Order;
use InvalidArgumentException;

final class DoctrineCriteriaConverter
{
    public static function convert(Criteria $criteria, ?FilterFieldsMap $filterFieldsMap = null): DoctrineCriteria
    {
        $expr = new ExpressionBuilder();
        $expression = self::buildExpression($expr, $criteria->filtersGroups(), $filterFieldsMap);

        $doctrineCriteria = new DoctrineCriteria();
        if ($expression !== null) {
            $doctrineCriteria->where($expression);
        }

        if ($criteria->order()->orderType() !== OrderType::NONE) {
            $doctrineCriteria->orderBy([
                $criteria->order()->orderBy()->value() => Order::from($criteria->order()->orderType()->value)
            ]);
        }

        if ($criteria->offset() !== null) {
            $doctrineCriteria->setFirstResult($criteria->offset());
        }

        if ($criteria->limit() !== null) {
            $doctrineCriteria->setMaxResults($criteria->limit());
        }

        return $doctrineCriteria;
    }

    private static function buildExpression(
        ExpressionBuilder $expr,
        array $filtersGroups,
        ?FilterFieldsMap $filterFieldsMap = null
    ): ?Expression {
        $groupExpressions = [];

        foreach ($filtersGroups as $group) {
            $groupExprs = [];

            foreach ($group->filters() as $filter) {
                $groupExprs[] = self::filterToExpression($expr, $filter, $filterFieldsMap);
            }

            foreach ($group->filtersGroups() as $subGroup) {
                $nested = self::buildExpression($expr, [$subGroup], $filterFieldsMap);
                if ($nested !== null) {
                    $groupExprs[] = $nested;
                }
            }

            if (!empty($groupExprs)) {
                $logical = strtoupper($group->logicalOperatorBetweenFiltersInGroup());
                $groupExpressions[] = $logical === 'OR'
                    ? $expr->orX(...$groupExprs)
                    : $expr->andX(...$groupExprs);
            }
        }

        if (empty($groupExpressions)) {
            return null;
        }

        $finalExpr = array_shift($groupExpressions);

        foreach ($filtersGroups as $i => $group) {
            if (!isset($groupExpressions[$i])) {
                continue;
            }

            $logical = strtoupper($group->logicalOperatorWithPreviousGroup());
            $finalExpr = $logical === 'OR'
                ? $expr->orX($finalExpr, $groupExpressions[$i])
                : $expr->andX($finalExpr, $groupExpressions[$i]);
        }

        return $finalExpr;
    }

    private static function filterToExpression(
        ExpressionBuilder $expr,
        Filter $filter,
        ?FilterFieldsMap $filterFieldsMap = null
    ): Expression {
        $field = $filterFieldsMap
            ? $filterFieldsMap->mapValueToField($filter->field()->value())
            : $filter->field()->value();
        $value = $filter->value()->value();

        switch ($filter->operator()) {
            case FilterOperator::EQUAL:
                return $expr->eq($field, $value);
            case FilterOperator::NOT_EQUAL:
                return $expr->neq($field, $value);
            case FilterOperator::GT:
                return $expr->gt($field, $value);
            case FilterOperator::GTE:
                return $expr->gte($field, $value);
            case FilterOperator::LT:
                return $expr->lt($field, $value);
            case FilterOperator::LTE:
                return $expr->lte($field, $value);
            case FilterOperator::IN:
                if (!is_array($value)) {
                    throw new InvalidArgumentException(
                        sprintf('Expected array for IN operator, got %s', gettype($value))
                    );
                }
                return $expr->in($field, $value);

            case FilterOperator::CONTAINS:
                return $expr->contains($field, $value);

            default:
                throw new InvalidArgumentException(sprintf('Unsupported operator: %s', $filter->operator()->value));
        }
    }
}
