<?php

declare(strict_types=1);

namespace App\Core\Domain\Service;

interface WarningGeneratorInterface
{
    /**
     * Generates warnings for a specific type of objects.
     * Returns statistics: number of added, maintained, and closed warnings.
     *
     * @return array{added: int, maintained: int, closed: int}
     */
    public function generate(): array;

    public function getType(): string;
}
