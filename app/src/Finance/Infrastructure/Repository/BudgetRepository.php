<?php

declare(strict_types=1);

namespace App\Finance\Infrastructure\Repository;

use App\Finance\Domain\Entity\Budget;
use App\Finance\Domain\Repository\BudgetRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BudgetRepository extends ServiceEntityRepository implements BudgetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    public function save(Budget $budget): void
    {
        $this->getEntityManager()->persist($budget);
        $this->getEntityManager()->flush();
    }

    public function remove(Budget $budget): void
    {
        $this->getEntityManager()->remove($budget);
        $this->getEntityManager()->flush();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Budget
    {
        $result = parent::find($id, $lockMode, $lockVersion);

        if ($result !== null && $result->isDeleted()) {
            return null;
        }

        return $result;
    }

    public function findNegativeBudgets(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.balance < :zero')
            ->andWhere('b.deletedAt IS NULL')
            ->setParameter('zero', 0)
            ->getQuery()
            ->getResult();
    }
}
