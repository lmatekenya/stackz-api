<?php

namespace App\Controller;

use App\Repository\LeaderboardEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/leaderboard')]
class LeaderboardController extends AbstractController
{
    public function __construct(
        private LeaderboardEntryRepository $leaderboardRepository
    ) {}

    #[Route('', name: 'api_leaderboard', methods: ['GET'])]
    public function getLeaderboard(Request $request): JsonResponse
    {
        $period = $request->query->get('period', 'all');
        $scope = $request->query->get('scope', 'global');
        $category = $request->query->get('category');

        $entries = [];

        if ($category) {
            $entries = $this->leaderboardRepository->findTopEntriesByCategory($category, 50);
        } else {
            $entries = $this->leaderboardRepository->findTopEntries(50);
        }

        $leaderboardData = [];
        foreach ($entries as $index => $entry) {
            $leaderboardData[] = [
                'rank' => $index + 1,
                'score' => $entry->getScore(),
                'timeElapsed' => $entry->getTimeElapsed(),
                'quizTitle' => $entry->getQuizTitle(),
                'date' => $entry->getDate()->getTimestamp() * 1000,
                'user' => [
                    'username' => $entry->getUser()->getUsername(),
                    'level' => $entry->getUser()->getLevel()
                ]
            ];
        }

        return new JsonResponse($leaderboardData);
    }
}
