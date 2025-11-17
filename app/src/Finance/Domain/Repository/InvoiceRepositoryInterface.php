<?php

declare(strict_types=1);

namespace App\Finance\Domain\Repository;

use App\Finance\Domain\Entity\Invoice;

interface InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void;

    public function remove(Invoice $invoice): void;

    public function findAll(): array;

    public function find(int $id): ?Invoice;

    public function findOverdueInvoices(): array;
}
