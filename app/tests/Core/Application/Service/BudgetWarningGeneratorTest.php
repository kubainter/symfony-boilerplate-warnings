<?php

declare(strict_types=1);

namespace App\Tests\Core\Application\Service;

use App\Core\Application\Service\BudgetWarningGenerator;
use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use App\Finance\Domain\Entity\Budget;
use App\Finance\Domain\Repository\BudgetRepositoryInterface;
use PHPUnit\Framework\TestCase;

class BudgetWarningGeneratorTest extends TestCase
{
    public function testGeneratesWarningForNegativeBudget(): void
    {
        $budget = new Budget('Negative Budget', -100.0);
        $reflection = new \ReflectionClass($budget);
        $property = $reflection->getProperty('id');
        $property->setValue($budget, 1);
        $this->assertSame(1, $budget->getId(), 'Budget id should be set to 1 for test');

        $budgetRepository = $this->createMock(BudgetRepositoryInterface::class);
        $budgetRepository->method('findNegativeBudgets')->willReturn([$budget]);

        $warningRepository = $this->createMock(WarningRepositoryInterface::class);
        $warningRepository->method('findActiveWarningForObject')->willReturn(null);
        $warningRepository->expects($this->once())->method('save');

        $generator = new BudgetWarningGenerator($budgetRepository, $warningRepository);
        $stats = $generator->generate();

        $this->assertSame(1, $stats['added']);
        $this->assertSame(0, $stats['maintained']);
        $this->assertSame(0, $stats['closed']);
    }
}
