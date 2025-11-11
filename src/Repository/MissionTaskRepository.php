<?php

namespace App\Repository;

use App\Entity\MissionTask;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MissionTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissionTask::class);
    }

    public function findCompletedTasks(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.dailyMission', 'm')
            ->where('m.user = :user')
            ->andWhere('t.progress >= t.target')
            ->andWhere('t.isClaimed = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findClaimableTasks(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.dailyMission', 'm')
            ->where('m.user = :user')
            ->andWhere('t.progress >= t.target')
            ->andWhere('t.isClaimed = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
