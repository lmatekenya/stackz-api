<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\QuizSession;
use App\Entity\ActivityLog;
use App\Entity\DailyMission;
use App\Entity\MissionTask;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class QuizController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    #[Route('/quizzes', name: 'quizzes_list', methods: ['GET'])]
    public function getQuizzes(): JsonResponse
    {
        $quizzes = $this->entityManager->getRepository(Quiz::class)->findAll();

        return $this->json(
            json_decode($this->serializer->serialize($quizzes, 'json', ['groups' => ['quiz:read']]))
        );
    }

    #[Route('/quizzes/{id}', name: 'quiz_get', methods: ['GET'])]
    public function getQuiz(int $id): JsonResponse
    {
        $quiz = $this->entityManager->getRepository(Quiz::class)->find($id);

        if (!$quiz) {
            return $this->json(['error' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(
            json_decode($this->serializer->serialize($quiz, 'json', ['groups' => ['quiz:read']]))
        );
    }

    #[Route('/quiz-sessions', name: 'quiz_session_create', methods: ['POST'])]
    public function createQuizSession(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        // Validate required fields
        $requiredFields = ['quizId', 'score', 'totalQuestions', 'correctAnswers', 'timeElapsed', 'cleared'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => "Field {$field} is required"], Response::HTTP_BAD_REQUEST);
            }
        }

        $quiz = $this->entityManager->getRepository(Quiz::class)->find($data['quizId']);
        if (!$quiz) {
            return $this->json(['error' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
        }

        // Create quiz session
        $session = new QuizSession();
        $session->setUser($user)
            ->setQuiz($quiz)
            ->setScore($data['score'])
            ->setTotalQuestions($data['totalQuestions'])
            ->setCorrectAnswers($data['correctAnswers'])
            ->setTimeElapsed($data['timeElapsed'])
            ->setCleared($data['cleared'])
            ->setCompletedAt(new \DateTimeImmutable());

        // Create activity log
        $activityLog = new ActivityLog();
        $activityLog->setUser($user)
            ->setQuiz($quiz)
            ->setCategory($quiz->getCategory())
            ->setScore($data['score'])
            ->setCorrectAnswers($data['correctAnswers'])
            ->setTimeElapsed($data['timeElapsed']);

        // Update user stats
        $user->setQuizzesCompleted($user->getQuizzesCompleted() + 1)
            ->setTotalPoints($user->getTotalPoints() + $data['score']);

        // Calculate rewards
        $currencyReward = $data['cleared'] ? 50 : 20;
        $xpReward = $data['cleared'] ? 100 : 50;

        $user->addCurrencyBalance($currencyReward, 'quiz_completion', 'Quiz completion reward')
            ->addXp($xpReward);

        // Update daily mission
        $this->updateDailyMission($user);

        $this->entityManager->persist($session);
        $this->entityManager->persist($activityLog);
        $this->entityManager->flush();

        return $this->json([
            'leaderboardEntry' => [
                'score' => $session->getScore(),
                'timeElapsed' => $session->getTimeElapsed(),
                'quizId' => $quiz->getId(),
                'quizTitle' => $quiz->getTitle(),
                'date' => $session->getCompletedAt()->getTimestamp()
            ],
            'stats' => $this->getUserQuizStats($user->getId()),
            'currencyAwarded' => $currencyReward,
            'xpAwarded' => $xpReward
        ], Response::HTTP_CREATED);
    }

    private function updateDailyMission(User $user): void
    {
        $today = new \DateTime();
        $mission = $this->entityManager->getRepository(DailyMission::class)->findOneBy([
            'user' => $user,
            'date' => $today
        ]);

        if (!$mission) {
            $mission = new DailyMission();
            $mission->setUser($user)
                ->setDate($today);

            // Check yesterday's mission for streak calculation
            $yesterday = (clone $today)->modify('-1 day');
            $yesterdayMission = $this->entityManager->getRepository(DailyMission::class)->findOneBy([
                'user' => $user,
                'date' => $yesterday
            ]);

            $streak = $yesterdayMission && $yesterdayMission->isCompleted() ? $yesterdayMission->getStreak() + 1 : 1;
            $mission->setStreak($streak);

            // Generate tasks
            $this->generateDailyTasks($mission);

            $this->entityManager->persist($mission);
        }

        $mission->setQuizzesCompleted($mission->getQuizzesCompleted() + 1);

        // Update tasks
        foreach ($mission->getTasks() as $task) {
            if (str_contains($task->getGoal(), 'complete quiz')) {
                $task->setProgress($task->getProgress() + 1);
            }
        }
    }

    private function generateDailyTasks(DailyMission $mission): void
    {
        $taskTemplates = [
            ['goal' => 'Complete 1 quiz', 'target' => 1, 'reward' => ['currency' => 25, 'xp' => 50]],
            ['goal' => 'Complete 3 quizzes', 'target' => 3, 'reward' => ['currency' => 50, 'xp' => 100]],
            ['goal' => 'Score 1000 points', 'target' => 1000, 'reward' => ['currency' => 75, 'xp' => 150]],
        ];

        foreach ($taskTemplates as $template) {
            $task = new MissionTask();
            $task->setDailyMission($mission)
                ->setGoal($template['goal'])
                ->setTarget($template['target'])
                ->setReward($template['reward'])
                ->setDescription("Complete {$template['target']} {$template['goal']}");

            $mission->addTask($task);
        }
    }

    private function getUserQuizStats(int $userId): array
    {
        // This would typically query a stats view or calculate aggregates
        // For now, return a simplified version
        return [
            'totalQuizzes' => $this->entityManager->getRepository(QuizSession::class)
                ->count(['user' => $userId]),
            'totalPoints' => $this->entityManager->getRepository(User::class)
                ->find($userId)->getTotalPoints(),
            'averageScore' => 750, // This would be calculated
        ];
    }

    #[Route('/users/{id}/quiz-stats', name: 'user_quiz_stats', methods: ['GET'])]
    public function getUserQuizStatsEndpoint(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Users can only access their own stats
        if ($user->getId() !== $this->getUser()->getId()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $stats = $this->getUserQuizStats($id);

        return $this->json(['quizStats' => $stats]);
    }

    #[Route('/users/{id}/activity', name: 'user_activity', methods: ['GET'])]
    public function getUserActivity(int $id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if ($user->getId() !== $this->getUser()->getId()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $page = $request->query->get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $activityLogs = $this->entityManager->getRepository(ActivityLog::class)
            ->findBy(
                ['user' => $user],
                ['timestamp' => 'DESC'],
                $limit,
                $offset
            );

        return $this->json(
            json_decode($this->serializer->serialize($activityLogs, 'json', ['groups' => ['activity:read']]))
        );
    }
}
