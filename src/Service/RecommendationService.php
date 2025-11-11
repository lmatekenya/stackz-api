<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Quiz;
use App\Repository\ActivityLogRepository;
use App\Repository\QuizRepository;
use App\Repository\QuizStatsRepository;

class RecommendationService
{
    public function __construct(
        private QuizRepository $quizRepository,
        private ActivityLogRepository $activityLogRepository,
        private QuizStatsRepository $quizStatsRepository
    ) {}

    public function getRecommendations(User $user): array
    {
        $continueQuizzes = $this->getContinueQuizzes($user);
        $improveQuizzes = $this->getImproveQuizzes($user);
        $newQuizzes = $this->getNewQuizzes($user);

        return [
            'continue' => array_slice($continueQuizzes, 0, 10),
            'improve' => array_slice($improveQuizzes, 0, 10),
            'newForYou' => array_slice($newQuizzes, 0, 10)
        ];
    }

    private function getContinueQuizzes(User $user): array
    {
        // Get quizzes with recent activity but not completed
        $recentActivity = $this->activityLogRepository->findBy(
            ['user' => $user],
            ['timestamp' => 'DESC'],
            10
        );

        $continueIds = [];
        foreach ($recentActivity as $activity) {
            $quiz = $activity->getQuiz();
            if ($quiz && $activity->getScore() < 80) { // Less than 80% score
                $continueIds[] = $quiz->getId();
            }
        }

        if (empty($continueIds)) {
            return [];
        }

        return $this->quizRepository->findBy(['id' => $continueIds]);
    }

    private function getImproveQuizzes(User $user): array
    {
        // Get quizzes with lowest performance
        $quizStats = $this->quizStatsRepository->findBy(
            ['user' => $user],
            ['winRate' => 'ASC'],
            20
        );

        $improveIds = [];
        foreach ($quizStats as $stats) {
            if ($stats->getWinRate() < 60) { // Less than 60% win rate
                $improveIds[] = $stats->getQuiz()->getId();
            }
        }

        if (empty($improveIds)) {
            return [];
        }

        return $this->quizRepository->findBy(['id' => $improveIds]);
    }

    private function getNewQuizzes(User $user): array
    {
        // Get quizzes never attempted
        $attemptedQuizIds = array_map(
            fn($stats) => $stats->getQuiz()->getId(),
            $this->quizStatsRepository->findBy(['user' => $user])
        );

        $newQuizzes = $this->quizRepository->createQueryBuilder('q')
            ->where('q.id NOT IN (:attempted)')
            ->setParameter('attempted', $attemptedQuizIds ?: [0])
            ->orderBy('RAND()')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        // Score by category affinity
        $categoryAffinity = $this->calculateCategoryAffinity($user);
        $scoredQuizzes = [];

        foreach ($newQuizzes as $quiz) {
            $score = $categoryAffinity[$quiz->getCategory()] ?? 50;
            $score += $this->calculateNoveltyScore($quiz, $user);

            $scoredQuizzes[] = [
                'quiz' => $quiz,
                'score' => $score
            ];
        }

        usort($scoredQuizzes, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_map(fn($item) => $item['quiz'], $scoredQuizzes);
    }

    private function calculateCategoryAffinity(User $user): array
    {
        $categoryStats = $user->getCategoryStats();
        $affinity = [];

        foreach ($categoryStats as $category => $stats) {
            if ($stats['attempts'] > 0) {
                $successRate = $stats['clears'] / $stats['attempts'];
                $affinity[$category] = $successRate * 100;
            }
        }

        return $affinity;
    }

    private function calculateNoveltyScore(Quiz $quiz, User $user): float
    {
        // Simple novelty scoring based on tags and categories
        $score = 0;

        // Bonus for categories with few attempts
        $categoryStats = $user->getCategoryStats();
        $categoryAttempts = $categoryStats[$quiz->getCategory()]['attempts'] ?? 0;

        if ($categoryAttempts < 5) {
            $score += 30;
        }

        // Bonus for new tags
        $userTags = $this->getUserPreferredTags($user);
        $quizTags = $quiz->getTags();

        $newTags = array_diff($quizTags, $userTags);
        $score += count($newTags) * 5;

        return $score;
    }

    private function getUserPreferredTags(User $user): array
    {
        $activityLogs = $this->activityLogRepository->findBy(
            ['user' => $user],
            ['timestamp' => 'DESC'],
            50
        );

        $tags = [];
        foreach ($activityLogs as $log) {
            $quiz = $log->getQuiz();
            if ($quiz) {
                $tags = array_merge($tags, $quiz->getTags());
            }
        }

        return array_unique($tags);
    }
}
