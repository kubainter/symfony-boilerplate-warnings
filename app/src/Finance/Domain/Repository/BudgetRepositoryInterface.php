<?php

declare(strict_types=1);

namespace App\Finance\Domain\Repository;

use App\Finance\Domain\Entity\Budget;

interface BudgetRepositoryInterface
{
    public function save(Budget $budget): void;

    public function remove(Budget $budget): void;

    public function findAll(): array;

    public function find(int $id): ?Budget;

    public function findNegativeBudgets(): array;
}
