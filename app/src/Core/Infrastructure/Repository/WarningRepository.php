<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\Entity\Warning;
use App\Core\Domain\Repository\WarningRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WarningRepository extends ServiceEntityRepository implements WarningRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Warning::class);
    }

    public function save(Warning $warning): void
    {
        $this->getEntityManager()->persist($warning);
        $this->getEntityManager()->flush();
    }

    public function remove(Warning $warning): void
    {
        $this->getEntityManager()->remove($warning);
        $this->getEntityManager()->flush();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('w')
            ->getQuery()
            ->getResult();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Warning
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    public function findActiveWarningForObject(string $objectType, int $objectId, string $category): ?Warning
    {
        return $this->createQueryBuilder('w')
            ->where('w.objectType = :objectType')
            ->andWhere('w.objectId = :objectId')
            ->andWhere('w.category = :category')
            ->andWhere('w.deletedAt IS NULL')
            ->setParameter('objectType', $objectType)
            ->setParameter('objectId', $objectId)
            ->setParameter('category', $category)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findActiveByCategory(string $category): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.category = :category')
            ->andWhere('w.deletedAt IS NULL')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }

    public function markAllAsDeletedForObject(string $objectType, int $objectId): void
    {
        $warnings = $this->createQueryBuilder('w')
            ->where('w.objectType = :objectType')
            ->andWhere('w.objectId = :objectId')
            ->andWhere('w.deletedAt IS NULL')
            ->setParameter('objectType', $objectType)
            ->setParameter('objectId', $objectId)
            ->getQuery()
            ->getResult();

        foreach ($warnings as $warning) {
            $warning->delete();
            $this->save($warning);
        }
    }
}
