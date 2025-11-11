<?php

namespace App\DataFixtures;

use App\Entity\Badge;
use App\Entity\Quiz;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadBadges($manager);
        $this->loadSampleQuizzes($manager);

        $manager->flush();
    }

    private function loadBadges(ObjectManager $manager): void
    {
        $badges = [
            [
                'name' => 'Quiz Novice',
                'description' => 'Complete your first quiz',
                'tier' => 'bronze',
                'requiredXp' => 100,
                'icon' => '🎯'
            ],
            [
                'name' => 'Speed Demon',
                'description' => 'Complete a quiz in under 60 seconds',
                'tier' => 'silver',
                'requiredXp' => 500,
                'icon' => '⚡'
            ],
            [
                'name' => 'Perfect Score',
                'description' => 'Get 100% on any quiz',
                'tier' => 'gold',
                'requiredXp' => 1000,
                'icon' => '💯'
            ],
            [
                'name' => 'Category Master',
                'description' => 'Master any category with 90%+ accuracy',
                'tier' => 'platinum',
                'requiredXp' => 2000,
                'requiredMastery' => 90,
                'icon' => '🏆'
            ]
        ];

        foreach ($badges as $badgeData) {
            $badge = new Badge();
            $badge->setName($badgeData['name']);
            $badge->setDescription($badgeData['description']);
            $badge->setTier($badgeData['tier']);
            $badge->setRequiredXp($badgeData['requiredXp']);
            $badge->setRequiredMastery($badgeData['requiredMastery'] ?? null);
            $badge->setIcon($badgeData['icon']);

            $manager->persist($badge);
        }
    }

    private function loadSampleQuizzes(ObjectManager $manager): void
    {
        $quizzes = [
            [
                'title' => 'General Knowledge Challenge',
                'category' => 'general',
                'difficulty' => 'medium',
                'gradient' => ['#667eea', '#764ba2'],
                'icon' => '🧠',
                'tags' => ['knowledge', 'trivia', 'fun'],
                'questions' => [
                    [
                        'text' => 'What is the capital of France?',
                        'options' => ['London', 'Berlin', 'Paris', 'Madrid'],
                        'correctAnswer' => 'Paris',
                        'timeLimit' => 30
                    ],
                    [
                        'text' => 'Which planet is known as the Red Planet?',
                        'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                        'correctAnswer' => 'Mars',
                        'timeLimit' => 30
                    ]
                ]
            ],
            [
                'title' => 'Science Fundamentals',
                'category' => 'science',
                'difficulty' => 'easy',
                'gradient' => ['#f093fb', '#f5576c'],
                'icon' => '🔬',
                'tags' => ['science', 'education', 'basics'],
                'questions' => [
                    [
                        'text' => 'What is H2O commonly known as?',
                        'options' => ['Oxygen', 'Hydrogen', 'Water', 'Carbon Dioxide'],
                        'correctAnswer' => 'Water',
                        'timeLimit' => 25
                    ],
                    [
                        'text' => 'How many bones are in the human body?',
                        'options' => ['106', '206', '306', '406'],
                        'correctAnswer' => '206',
                        'timeLimit' => 25
                    ]
                ]
            ]
        ];

        foreach ($quizzes as $quizData) {
            $quiz = new Quiz();
            $quiz->setTitle($quizData['title']);
            $quiz->setCategory($quizData['category']);
            $quiz->setDifficulty($quizData['difficulty']);
            $quiz->setGradient($quizData['gradient']);
            $quiz->setIcon($quizData['icon']);
            $quiz->setTags($quizData['tags']);
            $quiz->setBasePoints(100);
            $quiz->setTimeLimit(300);

            foreach ($quizData['questions'] as $questionData) {
                $question = new Question();
                $question->setQuiz($quiz);
                $question->setText($questionData['text']);
                $question->setOptions($questionData['options']);
                $question->setCorrectAnswer($questionData['correctAnswer']);
                $question->setTimeLimit($questionData['timeLimit']);
                $question->setType('multiple_choice');

                $manager->persist($question);
                $quiz->addQuestion($question);
            }

            $manager->persist($quiz);
        }
    }
}
