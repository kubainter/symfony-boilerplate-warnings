<?php

declare(strict_types=1);

namespace App\Finance\Infrastructure\Repository;

use App\Finance\Domain\Entity\Contractor;
use App\Finance\Domain\Repository\ContractorRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContractorRepository extends ServiceEntityRepository implements ContractorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contractor::class);
    }

    public function save(Contractor $contractor): void
    {
        $this->getEntityManager()->persist($contractor);
        $this->getEntityManager()->flush();
    }

    public function remove(Contractor $contractor): void
    {
        $this->getEntityManager()->remove($contractor);
        $this->getEntityManager()->flush();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Contractor
    {
        $result = parent::find($id, $lockMode, $lockVersion);

        if ($result !== null && $result->isDeleted()) {
            return null;
        }

        return $result;
    }

    public function findWithOverdueInvoicesExceedingLimit(float $limit): array
    {
        // This should be optimized in a real implementation
        $contractors = $this->findAll();

        return array_filter($contractors, function (Contractor $contractor) use ($limit) {
            return $contractor->hasExceededOverdueLimit($limit);
        });
    }
}
