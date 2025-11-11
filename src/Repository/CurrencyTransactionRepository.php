<?php

namespace App\Repository;

use App\Entity\CurrencyTransaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyTransaction::class);
    }

    public function findUserTransactions(User $user, int $limit = 100): array
    {
        return $this->createQueryBuilder('ct')
            ->where('ct.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ct.timestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findTransactionsByType(User $user, string $type, int $limit = 50): array
    {
        return $this->createQueryBuilder('ct')
            ->where('ct.user = :user')
            ->andWhere('ct.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->orderBy('ct.timestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getTotalEarned(User $user): int
    {
        $result = $this->createQueryBuilder('ct')
            ->select('SUM(ct.amount) as total')
            ->where('ct.user = :user')
            ->andWhere('ct.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', 'earn')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int) $result : 0;
    }

    public function getTotalSpent(User $user): int
    {
        $result = $this->createQueryBuilder('ct')
            ->select('SUM(ct.amount) as total')
            ->where('ct.user = :user')
            ->andWhere('ct.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', 'spend')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int) $result : 0;
    }
}
