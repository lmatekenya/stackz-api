<?php

namespace App\Repository;

use App\Entity\QuizPack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuizPackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizPack::class);
    }

    public function findPublicQuizPacks(): array
    {
        return $this->createQueryBuilder('qp')
            ->where('qp.isPublic = true')
            ->orderBy('qp.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDifficulty(string $difficulty): array
    {
        return $this->createQueryBuilder('qp')
            ->where('qp.difficulty = :difficulty')
            ->andWhere('qp.isPublic = true')
            ->setParameter('difficulty', $difficulty)
            ->getQuery()
            ->getResult();
    }

    public function searchByName(string $name): array
    {
        return $this->createQueryBuilder('qp')
            ->where('qp.name LIKE :name')
            ->andWhere('qp.isPublic = true')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }
}
