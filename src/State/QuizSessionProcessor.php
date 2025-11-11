<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\QuizSession;
use App\Entity\ActivityLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class QuizSessionProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof QuizSession && $operation->getName() === 'post') {
            $user = $this->security->getUser();

            if (!$user) {
                throw new \RuntimeException('User must be authenticated to create quiz session');
            }

            $data->setUser($user);
            $data->setCompletedAt(new \DateTimeImmutable());

            // Create activity log
            $activityLog = new ActivityLog();
            $activityLog->setUser($user)
                ->setQuiz($data->getQuiz())
                ->setCategory($data->getQuiz()->getCategory())
                ->setScore($data->getScore())
                ->setCorrectAnswers($data->getCorrectAnswers())
                ->setTimeElapsed($data->getTimeElapsed());

            // Update user stats
            $user->setQuizzesCompleted($user->getQuizzesCompleted() + 1)
                ->setTotalPoints($user->getTotalPoints() + $data->getScore());

            // Calculate rewards
            $currencyReward = $data->isCleared() ? 50 : 20;
            $xpReward = $data->isCleared() ? 100 : 50;

            $user->addCurrency($currencyReward, 'quiz_completion', 'Quiz completion reward')
                ->addXp($xpReward);

            $this->entityManager->persist($data);
            $this->entityManager->persist($activityLog);
            $this->entityManager->flush();

            // You can return additional data if needed
            return $data;
        }

        return $data;
    }
}
