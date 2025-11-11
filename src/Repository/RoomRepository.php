<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findActiveRooms(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status IN (:activeStatuses)')
            ->setParameter('activeStatuses', ['waiting', 'active'])
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPublicRooms(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.isPublic = true')
            ->andWhere('r.status = :status')
            ->setParameter('status', 'waiting')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUserRooms(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.participants', 'p')
            ->where('r.host = :user')
            ->orWhere('p = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCode(string $code): ?Room
    {
        return $this->createQueryBuilder('r')
            ->where('r.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
