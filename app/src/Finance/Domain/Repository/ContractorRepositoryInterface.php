<?php

declare(strict_types=1);

namespace App\Finance\Domain\Repository;

use App\Finance\Domain\Entity\Contractor;

interface ContractorRepositoryInterface
{
    public function save(Contractor $contractor): void;

    public function remove(Contractor $contractor): void;

    public function findAll(): array;

    public function find(int $id): ?Contractor;

    public function findWithOverdueInvoicesExceedingLimit(float $limit): array;
}
