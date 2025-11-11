<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }

    public function findByDifficulty(string $difficulty): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.difficulty = :difficulty')
            ->setParameter('difficulty', $difficulty)
            ->getQuery()
            ->getResult();
    }

    public function searchByTags(array $tags): array
    {
        $qb = $this->createQueryBuilder('q');

        foreach ($tags as $index => $tag) {
            $qb->orWhere("JSON_CONTAINS(q.tags, :tag$index) = 1")
                ->setParameter("tag$index", json_encode($tag));
        }

        return $qb->getQuery()->getResult();
    }
}
