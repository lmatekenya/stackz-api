<?php

namespace App\Repository;

use App\Entity\ActivityLog;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActivityLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityLog::class);
    }

    public function findRecentActivity(User $user, int $limit = 100): array
    {
        return $this->createQueryBuilder('al')
            ->where('al.user = :user')
            ->setParameter('user', $user)
            ->orderBy('al.timestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findActivityByDateRange(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('al')
            ->where('al.user = :user')
            ->andWhere('al.timestamp BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('al.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCategoryPerformance(User $user): array
    {
        return $this->createQueryBuilder('al')
            ->select('al.category, COUNT(al.id) as attempts, AVG(al.score) as avgScore, AVG(al.timeElapsed) as avgTime')
            ->where('al.user = :user')
            ->setParameter('user', $user)
            ->groupBy('al.category')
            ->getQuery()
            ->getResult();
    }
}
