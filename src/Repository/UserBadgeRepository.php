<?php

namespace App\Repository;

use App\Entity\Badge;
use App\Entity\UserBadge;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserBadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBadge::class);
    }

    public function findUserBadges(User $user): array
    {
        return $this->createQueryBuilder('ub')
            ->join('ub.badge', 'b')
            ->addSelect('b')
            ->where('ub.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.requiredXp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDisplayedBadges(User $user): array
    {
        return $this->createQueryBuilder('ub')
            ->join('ub.badge', 'b')
            ->addSelect('b')
            ->where('ub.user = :user')
            ->andWhere('ub.isDisplayed = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findRecentlyUnlocked(User $user, int $limit = 5): array
    {
        return $this->createQueryBuilder('ub')
            ->join('ub.badge', 'b')
            ->addSelect('b')
            ->where('ub.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ub.unlockedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function userHasBadge(User $user, Badge $badge): bool
    {
        $result = $this->createQueryBuilder('ub')
            ->select('COUNT(ub.id)')
            ->where('ub.user = :user')
            ->andWhere('ub.badge = :badge')
            ->setParameter('user', $user)
            ->setParameter('badge', $badge)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }
}
