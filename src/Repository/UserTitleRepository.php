<?php

namespace App\Repository;

use App\Entity\UserTitle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserTitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTitle::class);
    }

    public function findUserTitles(User $user): array
    {
        return $this->createQueryBuilder('ut')
            ->where('ut.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ut.unlockedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveTitle(User $user): ?UserTitle
    {
        return $this->createQueryBuilder('ut')
            ->where('ut.user = :user')
            ->andWhere('ut.isActive = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
