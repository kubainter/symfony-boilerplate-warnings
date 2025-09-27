<?php

declare(strict_types=1);

namespace App\Tests\Finance\Domain\Entity;

use App\Finance\Domain\Entity\Budget;
use PHPUnit\Framework\TestCase;

class BudgetTest extends TestCase
{
    public function testBudgetInitialization(): void
    {
        $budget = new Budget('Test Budget', 123.45);
        $this->assertSame('Test Budget', $budget->getName());
        $this->assertSame(123.45, $budget->getBalance());
        $this->assertNull($budget->getId());
    }
}
