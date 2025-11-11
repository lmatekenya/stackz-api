<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findByQuizAndDifficulty(int $quizId, string $difficulty): array
    {
        return $this->createQueryBuilder('q')
            ->join('q.quiz', 'quiz')
            ->where('quiz.id = :quizId')
            ->andWhere('quiz.difficulty = :difficulty')
            ->setParameter('quizId', $quizId)
            ->setParameter('difficulty', $difficulty)
            ->getQuery()
            ->getResult();
    }

    public function findRandomQuestions(int $limit = 10): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
