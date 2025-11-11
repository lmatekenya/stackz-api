<?php

namespace App\Repository;

use App\Entity\LeaderboardEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LeaderboardEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaderboardEntry::class);
    }

    public function findTopEntries(int $limit = 50): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.score', 'DESC')
            ->addOrderBy('l.timeElapsed', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findTopEntriesByCategory(string $category, int $limit = 50): array
    {
        return $this->createQueryBuilder('l')
            ->join('l.quiz', 'q')
            ->andWhere('q.category = :category')
            ->setParameter('category', $category)
            ->orderBy('l.score', 'DESC')
            ->addOrderBy('l.timeElapsed', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findUserRank(LeaderboardEntry $entry): int
    {
        $entries = $this->findTopEntries(1000);

        foreach ($entries as $index => $leaderboardEntry) {
            if ($leaderboardEntry->getId() === $entry->getId()) {
                return $index + 1;
            }
        }

        return 0;
    }
}
