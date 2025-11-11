<?php

namespace App\Repository;

use App\Entity\Badge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }

    public function findByTier(string $tier): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.tier = :tier')
            ->setParameter('tier', $tier)
            ->getQuery()
            ->getResult();
    }

    public function findEarnableBadges(int $userXp): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.requiredXp <= :userXp')
            ->setParameter('userXp', $userXp)
            ->orderBy('b.requiredXp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(?string $category): array
    {
        $qb = $this->createQueryBuilder('b');

        if ($category) {
            $qb->where('b.category = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }
}
