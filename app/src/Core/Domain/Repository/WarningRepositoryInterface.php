<?php

declare(strict_types=1);

namespace App\Core\Domain\Repository;

use App\Core\Domain\Entity\Warning;

interface WarningRepositoryInterface
{
    public function save(Warning $warning): void;

    public function remove(Warning $warning): void;

    public function findAll(): array;

    public function find(int $id): ?Warning;

    public function findActiveWarningForObject(string $objectType, int $objectId, string $category): ?Warning;

    public function findAllActive(): array;

    public function findActiveByCategory(string $category): array;

    public function markAllAsDeletedForObject(string $objectType, int $objectId): void;
}
