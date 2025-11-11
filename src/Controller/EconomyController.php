<?php

namespace App\Controller;

use App\Service\EconomyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users/{id}/currency')]
class EconomyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private EconomyService $economyService
    ) {}

    #[Route('/earn', name: 'api_currency_earn', methods: ['POST'])]
    public function earnCurrency(int $id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        $amount = $data['amount'] ?? null;
        $source = $data['source'] ?? null;
        $description = $data['description'] ?? null;

        if (!$amount || !$source) {
            return new JsonResponse(['error' => 'Amount and source are required'], Response::HTTP_BAD_REQUEST);
        }

        $transaction = $this->economyService->earnCurrency($user, $amount, $source, $description);

        return new JsonResponse([
            'balance' => $user->getCurrencyBalance(),
            'transaction' => [
                'id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'type' => $transaction->getType(),
                'source' => $transaction->getSource(),
                'description' => $transaction->getDescription(),
                'timestamp' => $transaction->getTimestamp()->getTimestamp() * 1000
            ]
        ]);
    }

    #[Route('/spend', name: 'api_currency_spend', methods: ['POST'])]
    public function spendCurrency(int $id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);

        $amount = $data['amount'] ?? null;
        $source = $data['source'] ?? null;
        $description = $data['description'] ?? null;

        if (!$amount || !$source) {
            return new JsonResponse(['error' => 'Amount and source are required'], Response::HTTP_BAD_REQUEST);
        }

        $transaction = $this->economyService->spendCurrency($user, $amount, $source, $description);

        if (!$transaction) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Insufficient funds'
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'success' => true,
            'balance' => $user->getCurrencyBalance(),
            'transaction' => [
                'id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'type' => $transaction->getType(),
                'source' => $transaction->getSource(),
                'description' => $transaction->getDescription(),
                'timestamp' => $transaction->getTimestamp()->getTimestamp() * 1000
            ]
        ]);
    }
}
