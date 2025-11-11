<?php

namespace App\Repository;

use App\Entity\DailyMission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DailyMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyMission::class);
    }

    public function findCurrentMission(User $user): ?DailyMission
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('m')
            ->where('m.user = :user')
            ->andWhere('m.date = :today')
            ->setParameter('user', $user)
            ->setParameter('today', $today->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserMissions(User $user, int $limit = 30): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.user = :user')
            ->setParameter('user', $user)
            ->orderBy('m.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findLongestStreak(User $user): int
    {
        $result = $this->createQueryBuilder('m')
            ->select('MAX(m.streak) as longest_streak')
            ->where('m.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int) $result : 0;
    }
}
