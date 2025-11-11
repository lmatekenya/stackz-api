<?php

namespace App\Service;

use App\Entity\CurrencyTransaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class EconomyService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function earnCurrency(User $user, int $amount, string $source, string $description): CurrencyTransaction
    {
        $user->setCurrencyBalance($user->getCurrencyBalance() + $amount);

        $transaction = new CurrencyTransaction();
        $transaction->setUser($user);
        $transaction->setAmount($amount);
        $transaction->setType('earn');
        $transaction->setSource($source);
        $transaction->setDescription($description);
        $transaction->setTimestamp(new \DateTime());

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }

    public function spendCurrency(User $user, int $amount, string $source, string $description): ?CurrencyTransaction
    {
        if ($user->getCurrencyBalance() < $amount) {
            return null;
        }

        $user->setCurrencyBalance($user->getCurrencyBalance() - $amount);

        $transaction = new CurrencyTransaction();
        $transaction->setUser($user);
        $transaction->setAmount($amount);
        $transaction->setType('spend');
        $transaction->setSource($source);
        $transaction->setDescription($description);
        $transaction->setTimestamp(new \DateTime());

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }

    public function getTransactionHistory(User $user, int $limit = 100): array
    {
        return $this->entityManager->getRepository(CurrencyTransaction::class)
            ->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.timestamp', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
