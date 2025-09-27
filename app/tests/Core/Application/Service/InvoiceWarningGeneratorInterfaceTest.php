<?php

declare(strict_types=1);

namespace App\Tests\Core\Application\Service;

use App\Core\Application\Service\InvoiceWarningGenerator;
use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use App\Finance\Domain\Repository\InvoiceRepositoryInterface;
use App\Finance\Domain\Entity\Invoice;
use App\Finance\Domain\Entity\Contractor;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class InvoiceWarningGeneratorInterfaceTest extends TestCase
{
    public function testMaintainsExistingWarning(): void
    {
        $contractor = new Contractor('Test Contractor');
        $reflectionContractor = new \ReflectionClass($contractor);
        $propertyContractor = $reflectionContractor->getProperty('id');
        $propertyContractor->setValue($contractor, 1);

        $invoice = new Invoice('INV-002', $contractor, 200.0, new DateTimeImmutable('-5 days'));
        $reflectionInvoice = new \ReflectionClass($invoice);
        $propertyInvoice = $reflectionInvoice->getProperty('id');
        $propertyInvoice->setValue($invoice, 2);

        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $invoiceRepository->method('findOverdueInvoices')->willReturn([$invoice]);

        $warningRepository = $this->createMock(WarningRepositoryInterface::class);
        $warningRepository->method('findActiveWarningForObject')->willReturn(
            new Warning('invoice', $invoice->getId(), 'invoice overdue')
        );
        $warningRepository->expects($this->never())->method('save');

        $generator = new InvoiceWarningGenerator($invoiceRepository, $warningRepository);
        $stats = $generator->generate();

        $this->assertSame(0, $stats['added']);
        $this->assertSame(1, $stats['maintained']);
        $this->assertSame(0, $stats['closed']);
    }
}
