<?php

namespace App\Repository;

use App\Entity\QuizStats;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizStatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizStats::class);
    }

    public function findUserStats(User $user): array
    {
        return $this->createQueryBuilder('qs')
            ->where('qs.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findCategoryStats(User $user): array
    {
        $stats = $this->createQueryBuilder('qs')
            ->select('quiz.category, COUNT(qs.id) as attempts, AVG(qs.winRate) as avgWinRate, MAX(qs.bestScore) as bestScore')
            ->join('qs.quiz', 'quiz')
            ->where('qs.user = :user')
            ->setParameter('user', $user)
            ->groupBy('quiz.category')
            ->getQuery()
            ->getResult();

        return $stats;
    }

    public function findOverallStats(User $user): array
    {
        $result = $this->createQueryBuilder('qs')
            ->select('COUNT(qs.id) as totalAttempts, SUM(qs.clears) as totalClears, AVG(qs.winRate) as overallWinRate, AVG(qs.avgTime) as avgTime')
            ->where('qs.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult();

        return $result;
    }
}
