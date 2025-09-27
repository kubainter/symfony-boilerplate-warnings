<?php

declare(strict_types=1);

namespace App\Tests\Core\Application\Service;

use App\Core\Application\Service\InvoiceWarningGenerator;
use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use App\Finance\Domain\Entity\Invoice;
use App\Finance\Domain\Repository\InvoiceRepositoryInterface;
use App\Finance\Domain\Entity\Contractor;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class InvoiceWarningGeneratorTest extends TestCase
{
    public function testGeneratesWarningForOverdueInvoice(): void
    {
        $contractor = new Contractor('Test Contractor');
        $reflectionContractor = new \ReflectionClass($contractor);
        $propertyContractor = $reflectionContractor->getProperty('id');
        $propertyContractor->setValue($contractor, 1);

        $invoice = new Invoice('INV-001', $contractor, 100.0, new DateTimeImmutable('-10 days'));
        $reflectionInvoice = new \ReflectionClass($invoice);
        $propertyInvoice = $reflectionInvoice->getProperty('id');
        $propertyInvoice->setValue($invoice, 1);
        $this->assertSame(1, $invoice->getId(), 'Invoice id should be set to 1 for test');

        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $invoiceRepository->method('findOverdueInvoices')->willReturn([$invoice]);

        $warningRepository = $this->createMock(WarningRepositoryInterface::class);
        $warningRepository->method('findActiveWarningForObject')->willReturn(null);
        $warningRepository->expects($this->once())->method('save');

        $generator = new InvoiceWarningGenerator($invoiceRepository, $warningRepository);
        $stats = $generator->generate();

        $this->assertSame(1, $stats['added']);
        $this->assertSame(0, $stats['maintained']);
        $this->assertSame(0, $stats['closed']);
    }
}
