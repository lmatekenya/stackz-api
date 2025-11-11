<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ActivityLogRepository;
use App\Repository\CurrencyTransactionRepository;
use App\Repository\UserBadgeRepository;
use App\Service\RecommendationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private SerializerInterface $serializer,
        private ActivityLogRepository $activityLogRepository,
        private CurrencyTransactionRepository $currencyTransactionRepository,
        private UserBadgeRepository $userBadgeRepository,
        private RecommendationService $recommendationService
    ) {}

    #[Route('/{id}', name: 'api_user_get', methods: ['GET'])]
    public function getUserProfile(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse(
            json_decode($this->serializer->serialize($user, 'json', ['groups' => ['user:read']]))
        );
    }

    #[Route('/{id}', name: 'api_user_update', methods: ['PATCH'])]
    public function updateUserProfile(int $id, Request $request): JsonResponse
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

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        if (isset($data['preferences'])) {
            $user->setPreferences(array_merge($user->getPreferences(), $data['preferences']));
        }

        $this->entityManager->flush();

        return new JsonResponse(
            json_decode($this->serializer->serialize($user, 'json', ['groups' => ['user:read']]))
        );
    }

    #[Route('/{id}/quiz-stats', name: 'api_user_quiz_stats', methods: ['GET'])]
    public function getUserQuizStats(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $quizStats = [];
        // Implementation would fetch and format quiz statistics
        // This is a simplified version

        return new JsonResponse(['quizStats' => $quizStats]);
    }

    #[Route('/{id}/activity', name: 'api_user_activity', methods: ['GET'])]
    public function getUserActivity(int $id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $activityLogs = $this->activityLogRepository->findBy(
            ['user' => $user],
            ['timestamp' => 'DESC'],
            $limit,
            $offset
        );

        $activityData = [];
        foreach ($activityLogs as $log) {
            $activityData[] = [
                'id' => $log->getId(),
                'category' => $log->getCategory(),
                'score' => $log->getScore(),
                'correctAnswers' => $log->getCorrectAnswers(),
                'timeElapsed' => $log->getTimeElapsed(),
                'timestamp' => $log->getTimestamp()->getTimestamp() * 1000
            ];
        }

        return new JsonResponse($activityData);
    }

    #[Route('/{id}/currency', name: 'api_user_currency', methods: ['GET'])]
    public function getUserCurrency(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $transactions = $this->currencyTransactionRepository->findUserTransactions($user, 50);

        $transactionData = [];
        foreach ($transactions as $transaction) {
            $transactionData[] = [
                'id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'type' => $transaction->getType(),
                'source' => $transaction->getSource(),
                'description' => $transaction->getDescription(),
                'timestamp' => $transaction->getTimestamp()->getTimestamp() * 1000
            ];
        }

        return new JsonResponse([
            'balance' => $user->getCurrencyBalance(),
            'transactions' => $transactionData
        ]);
    }

    #[Route('/{id}/badges', name: 'api_user_badges', methods: ['GET'])]
    public function getUserBadges(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userBadges = $this->userBadgeRepository->findUserBadges($user);

        $badgeData = [];
        foreach ($userBadges as $userBadge) {
            $badge = $userBadge->getBadge();
            $badgeData[] = [
                'id' => $badge->getId(),
                'name' => $badge->getName(),
                'description' => $badge->getDescription(),
                'icon' => $badge->getIcon(),
                'tier' => $badge->getTier(),
                'isDisplayed' => $userBadge->isDisplayed(),
                'isActive' => $userBadge->isActive(),
                'unlockedAt' => $userBadge->getUnlockedAt()->getTimestamp() * 1000
            ];
        }

        return new JsonResponse($badgeData);
    }

    #[Route('/{id}/recommendations', name: 'api_user_recommendations', methods: ['GET'])]
    public function getUserRecommendations(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        if ($currentUser->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $recommendations = $this->recommendationService->getRecommendations($user);

        $recommendationData = [];
        foreach ($recommendations as $type => $quizzes) {
            $recommendationData[$type] = array_map(function($quiz) {
                return [
                    'id' => $quiz->getId(),
                    'title' => $quiz->getTitle(),
                    'category' => $quiz->getCategory(),
                    'difficulty' => $quiz->getDifficulty(),
                    'gradient' => $quiz->getGradient(),
                    'icon' => $quiz->getIcon()
                ];
            }, $quizzes);
        }

        return new JsonResponse($recommendationData);
    }
}
