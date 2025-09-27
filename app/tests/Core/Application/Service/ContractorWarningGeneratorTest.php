<?php

declare(strict_types=1);

namespace App\Tests\Core\Application\Service;

use App\Core\Application\Service\ContractorWarningGenerator;
use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use App\Finance\Domain\Entity\Contractor;
use App\Finance\Domain\Repository\ContractorRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ContractorWarningGeneratorTest extends TestCase
{
    public function testGeneratesWarningForOverdueLimit(): void
    {
        $contractor = new Contractor('Test Contractor');
        $reflection = new \ReflectionClass($contractor);
        $property = $reflection->getProperty('id');
        $property->setValue($contractor, 1);
        $this->assertSame(1, $contractor->getId(), 'Contractor id should be set to 1 for test');

        $contractorRepository = $this->createMock(ContractorRepositoryInterface::class);
        $contractorRepository->method('findWithOverdueInvoicesExceedingLimit')->willReturn([$contractor]);

        $warningRepository = $this->createMock(WarningRepositoryInterface::class);
        $warningRepository->method('findActiveWarningForObject')->willReturn(null);
        $warningRepository->expects($this->once())->method('save');

        $generator = new ContractorWarningGenerator($contractorRepository, $warningRepository);
        $stats = $generator->generate();

        $this->assertSame(1, $stats['added']);
        $this->assertSame(0, $stats['maintained']);
        $this->assertSame(0, $stats['closed']);
    }
}
