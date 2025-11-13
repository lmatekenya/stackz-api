<?php

namespace App\Controller;

use App\Entity\MissionTask;
use App\Service\DailyMissionService;
use App\Service\EconomyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/missions')]
class DailyMissionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private DailyMissionService $missionService,
        private EconomyService $economyService
    ) {}

    #[Route('/daily', name: 'api_missions_daily', methods: ['GET'])]
    public function getDailyMission(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $date = new \DateTime($request->query->get('date', 'today'));
        $mission = $this->missionService->getOrCreateDailyMission($user, $date);

        $tasksData = [];
        foreach ($mission->getTasks() as $task) {
            $tasksData[] = [
                'id' => $task->getId(),
                'goal' => $task->getGoal(),
                'target' => $task->getTarget(),
                'progress' => $task->getProgress(),
                'reward' => $task->getReward(),
                'isClaimed' => $task->isClaimed(),
                'description' => $task->getDescription(),
                'category' => $task->getCategory(),
                'isCompleted' => $task->isCompleted()
            ];
        }

        return new JsonResponse([
            'mission' => [
                'id' => $mission->getId(),
                'date' => $mission->getDate()->format('Y-m-d'),
                'quizzesCompleted' => $mission->getQuizzesCompleted(),
                'target' => $mission->getTarget(),
                'streak' => $mission->getStreak(),
                'reward' => $mission->getReward()
            ],
            'tasks' => $tasksData
        ]);
    }

    #[Route('/tasks/{id}/progress', name: 'api_mission_task_progress', methods: ['POST'])]
    public function updateTaskProgress(int $id, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $task = $this->entityManager->getRepository(MissionTask::class)->find($id);
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        // Verify task belongs to user
        if ($task->getDailyMission()->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $progress = $data['progress'] ?? null;

        if ($progress === null) {
            return new JsonResponse(['error' => 'Progress is required'], Response::HTTP_BAD_REQUEST);
        }

        $task->setProgress((int) $progress);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $task->getId(),
            'progress' => $task->getProgress(),
            'isCompleted' => $task->isCompleted()
        ]);
    }

    #[Route('/tasks/{id}/claim', name: 'api_mission_task_claim', methods: ['POST'])]
    public function claimTaskReward(int $id): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $task = $this->entityManager->getRepository(MissionTask::class)->find($id);
        if (!$task) {
            return new JsonResponse(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        // Verify task belongs to user
        if ($task->getDailyMission()->getUser()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        if (!$task->isCompleted()) {
            return new JsonResponse(['error' => 'Task not completed'], Response::HTTP_BAD_REQUEST);
        }

        if ($task->isClaimed()) {
            return new JsonResponse(['error' => 'Reward already claimed'], Response::HTTP_BAD_REQUEST);
        }

        // Extract reward amount as integer
        $rewardAmount = is_array($task->getReward()) ? ($task->getReward()['amount'] ?? 0) : $task->getReward();


        // Award currency
        $transaction = $this->economyService->earnCurrency(
            $user,
            $rewardAmount,
            'mission_reward',
            "Mission completed: {$task->getDescription()}"
        );

        $task->setIsClaimed(true);
        $this->entityManager->flush();

        return new JsonResponse([
            'rewardClaimed' => $task->getReward(),
            'transaction' => [
                'id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'type' => $transaction->getType(),
                'description' => $transaction->getDescription(),
                'timestamp' => $transaction->getTimestamp()->getTimestamp() * 1000
            ],
            'newBalance' => $user->getCurrencyBalance()
        ]);
    }
}
