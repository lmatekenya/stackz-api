<?php

namespace App\Service;

use App\Entity\DailyMission;
use App\Entity\MissionTask;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DailyMissionService
{
    private array $missionTemplates = [
        [
            'goal' => 'complete_quizzes',
            'target' => 3,
            'reward' => 50,
            'description' => 'Complete 3 quizzes'
        ],
        [
            'goal' => 'correct_answers',
            'target' => 15,
            'reward' => 75,
            'description' => 'Get 15 correct answers'
        ],
        [
            'goal' => 'streak_days',
            'target' => 1,
            'reward' => 25,
            'description' => 'Maintain your streak'
        ],
        [
            'goal' => 'specific_category',
            'target' => 2,
            'reward' => 60,
            'description' => 'Complete 2 quizzes in a specific category',
            'category' => 'science'
        ]
    ];

    public function __construct(private EntityManagerInterface $entityManager) {}

    public function getOrCreateDailyMission(User $user, \DateTimeInterface $date): DailyMission
    {
        $missionRepo = $this->entityManager->getRepository(DailyMission::class);
        $existingMission = $missionRepo->findOneBy([
            'user' => $user,
            'date' => $date
        ]);

        if ($existingMission) {
            return $existingMission;
        }

        // Check if user maintained streak
        $yesterday = clone $date;
        $yesterday->modify('-1 day');
        $yesterdayMission = $missionRepo->findOneBy([
            'user' => $user,
            'date' => $yesterday
        ]);

        $streak = $yesterdayMission ? $yesterdayMission->getStreak() + 1 : 1;

        // Create new mission
        $mission = new DailyMission();
        $mission->setUser($user);
        $mission->setDate($date);
        $mission->setQuizzesCompleted(0);
        $mission->setTarget(5); // Default target
        $mission->setStreak($streak);
        $mission->setReward(100 + ($streak * 10)); // Base reward + streak bonus

        // Generate tasks
        $this->generateMissionTasks($mission);

        $this->entityManager->persist($mission);
        $this->entityManager->flush();

        return $mission;
    }

    private function generateMissionTasks(DailyMission $mission): void
    {
//        $selectedTemplates = array_rand($this->missionTemplates, 3);
//
//        if (!is_array($selectedTemplates)) {
//            $selectedTemplates = [$selectedTemplates];
//        }

        $numTasks = min(3, count($this->missionTemplates));
        $selectedKeys = array_rand($this->missionTemplates, $numTasks);
        if (!is_array($selectedKeys)) {
            $selectedKeys = [$selectedKeys];
        }

        foreach ($selectedKeys as $templateIndex) {
            $template = $this->missionTemplates[$templateIndex];

            $task = new MissionTask();
            $task->setDailyMission($mission);
            $task->setGoal($template['goal']);
            $task->setTarget($template['target']);
            $task->setProgress(0);
            $task->setReward($template['reward']);
            $task->setDescription($template['description']);
            $task->setIsClaimed(false);

            if (isset($template['category'])) {
                $task->setCategory($template['category']);
            }

            $this->entityManager->persist($task);
        }
    }

    public function updateMissionProgress(User $user, string $goalType, int $amount = 1, ?string $category = null): void
    {
        $today = new \DateTimeImmutable();
        $mission = $this->getOrCreateDailyMission($user, $today);

        foreach ($mission->getTasks() as $task) {
            if ($task->getGoal() === $goalType) {
                if ($category && $task->getCategory() && $task->getCategory() !== $category) {
                    continue;
                }

                $newProgress = min($task->getProgress() + $amount, $task->getTarget());
                $task->setProgress($newProgress);

                // Update mission completion
                if ($newProgress >= $task->getTarget()) {
                    $mission->setQuizzesCompleted($mission->getQuizzesCompleted() + 1);
                }
            }
        }

        $this->entityManager->flush();
    }
}
