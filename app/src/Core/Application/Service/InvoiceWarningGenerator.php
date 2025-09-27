<?php

declare(strict_types=1);

namespace App\Core\Application\Service;

use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use App\Core\Domain\Service\WarningGeneratorInterface;
use App\Finance\Domain\Repository\InvoiceRepositoryInterface;

class InvoiceWarningGenerator implements WarningGeneratorInterface
{
    private const TYPE = 'invoice';

    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
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

        $overdueInvoices = $this->invoiceRepository->findOverdueInvoices();

        foreach ($overdueInvoices as $invoice) {
            $existingWarning = $this->warningRepository->findActiveWarningForObject(
                self::TYPE,
                $invoice->getId(),
                Warning::CATEGORY_INVOICE_OVERDUE
            );

            if ($existingWarning === null) {
                $warning = Warning::createForOverdueInvoice($invoice->getId());
                $this->warningRepository->save($warning);
                $stats['added']++;
            } else {
                $stats['maintained']++;
            }
        }

        $activeWarnings = $this->warningRepository->findActiveByCategory(Warning::CATEGORY_INVOICE_OVERDUE);

        foreach ($activeWarnings as $warning) {
            $invoiceId = $warning->getObjectId();
            $stillOverdue = false;

            foreach ($overdueInvoices as $invoice) {
                if ($invoice->getId() === $invoiceId) {
                    $stillOverdue = true;
                    break;
                }
            }

            if (!$stillOverdue) {
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
