<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\CurrencyTransaction;
use App\Entity\LeaderboardEntry;
use App\Entity\Quiz;
use App\Entity\QuizStats;
use App\Entity\User;
use App\Service\DailyMissionService;
use App\Service\EconomyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/quiz-sessions')]
class QuizSessionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private EconomyService $economyService,
        private DailyMissionService $missionService
    ) {}

    #[Route('', name: 'api_quiz_session_create', methods: ['POST'])]
    public function createSession(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        // Validate required fields
        $requiredFields = ['quizId', 'score', 'totalQuestions', 'timeElapsed', 'cleared'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], Response::HTTP_BAD_REQUEST);
            }
        }

        $quiz = $this->entityManager->getRepository(Quiz::class)->find($data['quizId']);
        if (!$quiz) {
            return new JsonResponse(['error' => 'Quiz not found'], Response::HTTP_NOT_FOUND);
        }

        // Calculate XP and currency rewards
        $baseReward = $data['cleared'] ? 50 : 20;
        $timeBonus = max(0, (300 - $data['timeElapsed']) / 300 * 20); // Time bonus up to 20
        $scoreBonus = ($data['score'] / $data['totalQuestions']) * 30; // Score bonus up to 30

        $xpEarned = (int) ($baseReward + $timeBonus + $scoreBonus);
        $currencyEarned = $data['cleared'] ? 25 : 10;

        // Update user stats
        $user->setXp($user->getXp() + $xpEarned);
        $user->setTotalPoints($user->getTotalPoints() + $data['score']);
        $user->setQuizzesCompleted($user->getQuizzesCompleted() + 1);
        $user->setCurrencyBalance($user->getCurrencyBalance() + $currencyEarned);

        // Update category stats
        $categoryStats = $user->getCategoryStats();
        $category = $quiz->getCategory();
        if (!isset($categoryStats[$category])) {
            $categoryStats[$category] = [
                'attempts' => 0,
                'clears' => 0,
                'totalScore' => 0,
                'totalTime' => 0
            ];
        }

        $categoryStats[$category]['attempts']++;
        $categoryStats[$category]['totalScore'] += $data['score'];
        $categoryStats[$category]['totalTime'] += $data['timeElapsed'];
        if ($data['cleared']) {
            $categoryStats[$category]['clears']++;
        }

        $user->setCategoryStats($categoryStats);

        // Create leaderboard entry
        $leaderboardEntry = new LeaderboardEntry();
        $leaderboardEntry->setUser($user);
        $leaderboardEntry->setQuiz($quiz);
        $leaderboardEntry->setScore($data['score']);
        $leaderboardEntry->setTimeElapsed($data['timeElapsed']);
        $leaderboardEntry->setDate(new \DateTime());
        $leaderboardEntry->setQuizTitle($quiz->getTitle());

        // Update quiz stats
        $quizStatsRepo = $this->entityManager->getRepository(QuizStats::class);
        $quizStats = $quizStatsRepo->findOneBy(['user' => $user, 'quiz' => $quiz]);

        if (!$quizStats) {
            $quizStats = new QuizStats();
            $quizStats->setUser($user);
            $quizStats->setQuiz($quiz);
            $quizStats->setAttempts(0);
            $quizStats->setClears(0);
            $quizStats->setTotalTime(0);
            $quizStats->setTotalScore(0);
            $quizStats->setBestScore(0);
            $quizStats->setFastestTime(0);
        }

        $quizStats->setAttempts($quizStats->getAttempts() + 1);
        $quizStats->setTotalTime($quizStats->getTotalTime() + $data['timeElapsed']);
        $quizStats->setTotalScore($quizStats->getTotalScore() + $data['score']);

        if ($data['cleared']) {
            $quizStats->setClears($quizStats->getClears() + 1);
        }

        if ($data['score'] > $quizStats->getBestScore()) {
            $quizStats->setBestScore($data['score']);
        }

        if ($data['cleared'] && ($quizStats->getFastestTime() === 0 || $data['timeElapsed'] < $quizStats->getFastestTime())) {
            $quizStats->setFastestTime($data['timeElapsed']);
        }

        $quizStats->setLastPlayed(new \DateTime());
        $quizStats->setAvgTime($quizStats->getTotalTime() / $quizStats->getAttempts());
        $quizStats->setWinRate(($quizStats->getClears() / $quizStats->getAttempts()) * 100);

        // Create activity log
        $activityLog = new ActivityLog();
        $activityLog->setUser($user);
        $activityLog->setQuiz($quiz);
        $activityLog->setCategory($quiz->getCategory());
        $activityLog->setScore($data['score']);
        $activityLog->setCorrectAnswers($data['score']);
        $activityLog->setTimeElapsed($data['timeElapsed']);
        $activityLog->setTimestamp(new \DateTime());

        // Create currency transaction
        $currencyTransaction = new CurrencyTransaction();
        $currencyTransaction->setUser($user);
        $currencyTransaction->setAmount($currencyEarned);
        $currencyTransaction->setType('earn');
        $currencyTransaction->setSource('quiz_completion');
        $currencyTransaction->setDescription("Quiz completed: {$quiz->getTitle()}");
        $currencyTransaction->setTimestamp(new \DateTime());

        // Update daily mission progress
        $this->missionService->updateMissionProgress($user, 'quiz_completed');

        // Persist all entities
        $this->entityManager->persist($leaderboardEntry);
        $this->entityManager->persist($quizStats);
        $this->entityManager->persist($activityLog);
        $this->entityManager->persist($currencyTransaction);
        $this->entityManager->flush();

        return new JsonResponse([
            'leaderboardEntry' => [
                'id' => $leaderboardEntry->getId(),
                'score' => $leaderboardEntry->getScore(),
                'timeElapsed' => $leaderboardEntry->getTimeElapsed(),
                'quizTitle' => $leaderboardEntry->getQuizTitle(),
                'date' => $leaderboardEntry->getDate()->getTimestamp() * 1000
            ],
            'stats' => [
                'xpEarned' => $xpEarned,
                'currencyEarned' => $currencyEarned,
                'newLevel' => $user->getLevel(),
                'newBalance' => $user->getCurrencyBalance()
            ],
            'currencyAwarded' => $currencyEarned
        ], Response::HTTP_CREATED);
    }
}
