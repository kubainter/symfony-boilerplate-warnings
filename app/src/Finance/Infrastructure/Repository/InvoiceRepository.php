<?php

declare(strict_types=1);

namespace App\Finance\Infrastructure\Repository;

use App\Finance\Domain\Entity\Invoice;
use App\Finance\Domain\Repository\InvoiceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

class InvoiceRepository extends ServiceEntityRepository implements InvoiceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function save(Invoice $invoice): void
    {
        $this->getEntityManager()->persist($invoice);
        $this->getEntityManager()->flush();
    }

    public function remove(Invoice $invoice): void
    {
        $this->getEntityManager()->remove($invoice);
        $this->getEntityManager()->flush();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Invoice
    {
        $result = parent::find($id, $lockMode, $lockVersion);

        if ($result !== null && $result->isDeleted()) {
            return null;
        }

        return $result;
    }

    public function findOverdueInvoices(): array
    {
        $now = new DateTimeImmutable();

        return $this->createQueryBuilder('i')
            ->where('i.isPaid = :isPaid')
            ->andWhere('i.dueDate < :now')
            ->andWhere('i.deletedAt IS NULL')
            ->setParameter('isPaid', false)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }
}
