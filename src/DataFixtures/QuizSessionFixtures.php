<?php
// src/DataFixtures/QuizSessionFixtures.php
namespace App\DataFixtures;

use App\Entity\QuizSession;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\QuizFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class QuizSessionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $quizSession = new QuizSession();

            // Get references correctly
            $user = $this->getReference('user_' . (($i - 1) % 3 + 1), \App\Entity\User::class);
            $quiz = $this->getReference('quiz_' . (($i - 1) % 3 + 1), \App\Entity\Quiz::class);

            $quizSession->setUser($user);
            $quizSession->setQuiz($quiz);
            $quizSession->setScore(rand(0, 100));
            $quizSession->setTotalQuestions(rand(5, 20));
            $quizSession->setCorrectAnswers(rand(0, $quizSession->getTotalQuestions()));
            $quizSession->setTimeElapsed(rand(60, 600));
            $quizSession->setCleared((bool) rand(0, 1));
            $quizSession->setCompletedAt(new \DateTimeImmutable('-' . rand(0, 30) . ' days'));

            $manager->persist($quizSession);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            QuizFixtures::class, // Note: use QuizFixtures, not QuizPackFixtures
        ];
    }
}
