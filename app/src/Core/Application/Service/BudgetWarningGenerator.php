<?php

declare(strict_types=1);

namespace App\Core\Application\Service;

use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use App\Core\Domain\Service\WarningGeneratorInterface;
use App\Finance\Domain\Repository\BudgetRepositoryInterface;

class BudgetWarningGenerator implements WarningGeneratorInterface
{
    private const TYPE = 'budget';

    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepository,
        private readonly WarningRepositoryInterface $warningRepository
    ) {
    }

    /**
     * @return array{added: int, maintained: int, closed: int}
     */
    public function generate(): array
    {
        $stats = [
            'added' => 0,
            'maintained' => 0,
            'closed' => 0
        ];

        $negativeBudgets = $this->budgetRepository->findNegativeBudgets();

        foreach ($negativeBudgets as $budget) {
            $existingWarning = $this->warningRepository->findActiveWarningForObject(
                self::TYPE,
                $budget->getId(),
                Warning::CATEGORY_BUDGET_NEGATIVE
            );

            if ($existingWarning === null) {
                $warning = Warning::createForNegativeBudget($budget->getId());
                $this->warningRepository->save($warning);
                $stats['added']++;
            } else {
                $stats['maintained']++;
            }
        }

        $activeWarnings = $this->warningRepository->findActiveByCategory(Warning::CATEGORY_BUDGET_NEGATIVE);

        foreach ($activeWarnings as $warning) {
            $budgetId = $warning->getObjectId();
            $stillNegative = false;

            foreach ($negativeBudgets as $budget) {
                if ($budget->getId() === $budgetId) {
                    $stillNegative = true;
                    break;
                }
            }

            if (!$stillNegative) {
                $warning->delete();
                $this->warningRepository->save($warning);
                $stats['closed']++;
            }
        }

        return $stats;
    }

    public function getType(): string
    {
        return self::TYPE;
    }
}
