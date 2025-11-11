<?php

namespace App\Repository;

use App\Entity\QuestionSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionSet::class);
    }

    public function findByFormat(string $format): array
    {
        return $this->createQueryBuilder('qs')
            ->where('qs.format = :format')
            ->setParameter('format', $format)
            ->getQuery()
            ->getResult();
    }

    public function findPublicQuestionSets(): array
    {
        return $this->createQueryBuilder('qs')
            ->where('qs.isPublic = true')
            ->getQuery()
            ->getResult();
    }
}
