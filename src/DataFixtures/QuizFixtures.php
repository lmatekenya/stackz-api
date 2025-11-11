<?php
//
//namespace App\DataFixtures;
//
//use App\Entity\Quiz;
//use App\Entity\Question;
//use App\Entity\User;
//use Doctrine\Bundle\FixturesBundle\Fixture;
//use Doctrine\Persistence\ObjectManager;
//use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//
//class QuizFixtures extends Fixture
//{
//    public function __construct(
//        private UserPasswordHasherInterface $passwordHasher
//    ) {}
//
//    public function load(ObjectManager $manager): void
//    {
//        // Create test user
//        $user = new User();
//        $user->setEmail('test@example.com')
//            ->setUsername('testuser')
//            ->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
//        $manager->persist($user);
//
//        // Create sample quizzes
//        $quizzes = [
//            [
//                'title' => 'General Knowledge',
//                'category' => 'General',
//                'difficulty' => 'easy',
//                'gradient' => ['#667eea', '#764ba2'],
//                'icon' => '🧠',
//                'tags' => ['general', 'knowledge'],
//                'description' => 'Test your general knowledge with this fun quiz!',
//                'basePoints' => 100,
//                'timeLimit' => 300,
//                'questions' => [
//                    [
//                        'text' => 'What is the capital of France?',
//                        'options' => ['London', 'Paris', 'Berlin', 'Madrid'],
//                        'correctAnswers' => [1],
//                        'timeLimit' => 30
//                    ],
//                    [
//                        'text' => 'Which planet is known as the Red Planet?',
//                        'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
//                        'correctAnswers' => [1],
//                        'timeLimit' => 30
//                    ]
//                ]
//            ],
//            [
//                'title' => 'Science & Technology',
//                'category' => 'Science',
//                'difficulty' => 'medium',
//                'gradient' => ['#f093fb', '#f5576c'],
//                'icon' => '🔬',
//                'tags' => ['science', 'technology'],
//                'description' => 'Challenge yourself with science and technology questions',
//                'basePoints' => 150,
//                'timeLimit' => 400,
//                'questions' => [
//                    [
//                        'text' => 'What does CPU stand for?',
//                        'options' => ['Central Processing Unit', 'Computer Personal Unit', 'Central Processor Unit', 'Central Process Unit'],
//                        'correctAnswers' => [0],
//                        'timeLimit' => 25
//                    ]
//                ]
//            ]
//        ];
//
//        foreach ($quizzes as $quizData) {
//            $quiz = new Quiz();
//            $quiz->setTitle($quizData['title'])
//                ->setCategory($quizData['category'])
//                ->setDifficulty($quizData['difficulty'])
//                ->setGradient($quizData['gradient'])
//                ->setIcon($quizData['icon'])
//                ->setTags($quizData['tags'])
//                ->setDescription($quizData['description'])
//                ->setBasePoints($quizData['basePoints'])
//                ->setTimeLimit($quizData['timeLimit']);
//
//            foreach ($quizData['questions'] as $questionData) {
//                $question = new Question();
//                $question->setText($questionData['text'])
//                    ->setOptions($questionData['options'])
//                    ->setCorrectAnswers($questionData['correctAnswers'])
//                    ->setTimeLimit($questionData['timeLimit']);
//
//                $quiz->addQuestion($question);
//            }
//
//            $manager->persist($quiz);
//        }
//
//        $manager->flush();
//    }
//}


namespace App\DataFixtures;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class QuizFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Create test user
        $user = new User();
        $user->setEmail('tony@test.com')
            ->setUsername('tony')
            ->setPassword($this->passwordHasher->hashPassword($user, 'tony123'));
        $manager->persist($user);

        // Create sample quizzes
        $quizzes = [
            [
                'title' => 'General Knowledge',
                'category' => 'General',
                'difficulty' => 'easy',
                'gradient' => ['#667eea', '#764ba2'],
                'icon' => '🧠',
                'tags' => ['general', 'knowledge'],
                'description' => 'Test your general knowledge with this fun quiz!',
                'basePoints' => 100,
                'timeLimit' => 300,
                'questions' => [
                    [
                        'text' => 'What is the capital of France?',
                        'options' => ['London', 'Paris', 'Berlin', 'Madrid'],
                        'correctAnswer' => 'Paris', // Use string instead of array
                        'correctAnswers' => null,   // Can be null
                        'timeLimit' => 30
                    ],
                    [
                        'text' => 'Which planet is known as the Red Planet?',
                        'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                        'correctAnswer' => 'Mars',
                        'correctAnswers' => null,
                        'timeLimit' => 30
                    ],
                    [
                        'text' => 'What is 2 + 2?',
                        'options' => ['3', '4', '5', '6'],
                        'correctAnswer' => '4',
                        'correctAnswers' => null,
                        'timeLimit' => 20
                    ]
                ]
            ],
            [
                'title' => 'Science & Technology',
                'category' => 'Science',
                'difficulty' => 'medium',
                'gradient' => ['#f093fb', '#f5576c'],
                'icon' => '🔬',
                'tags' => ['science', 'technology'],
                'description' => 'Challenge yourself with science and technology questions',
                'basePoints' => 150,
                'timeLimit' => 400,
                'questions' => [
                    [
                        'text' => 'What does CPU stand for?',
                        'options' => [
                            'Central Processing Unit',
                            'Computer Personal Unit',
                            'Central Processor Unit',
                            'Central Process Unit'
                        ],
                        'correctAnswer' => 'Central Processing Unit',
                        'correctAnswers' => null,
                        'timeLimit' => 25
                    ],
                    [
                        'text' => 'Which of these is a programming language?',
                        'options' => ['HTML', 'CSS', 'Python', 'HTTP'],
                        'correctAnswer' => 'Python',
                        'correctAnswers' => null,
                        'timeLimit' => 25
                    ]
                ]
            ],
            [
                'title' => 'History',
                'category' => 'History',
                'difficulty' => 'hard',
                'gradient' => ['#4facfe', '#00f2fe'],
                'icon' => '📜',
                'tags' => ['history', 'world'],
                'description' => 'Test your knowledge of world history',
                'basePoints' => 200,
                'timeLimit' => 500,
                'questions' => [
                    [
                        'text' => 'In which year did World War II end?',
                        'options' => ['1944', '1945', '1946', '1947'],
                        'correctAnswer' => '1945',
                        'correctAnswers' => null,
                        'timeLimit' => 35
                    ]
                ]
            ]
        ];

        foreach ($quizzes as $quizData) {
            $quiz = new Quiz();
            $quiz->setTitle($quizData['title'])
                ->setCategory($quizData['category'])
                ->setDifficulty($quizData['difficulty'])
                ->setGradient($quizData['gradient'])
                ->setIcon($quizData['icon'])
                ->setTags($quizData['tags'])
                ->setDescription($quizData['description'])
                ->setBasePoints($quizData['basePoints'])
                ->setTimeLimit($quizData['timeLimit']);

            foreach ($quizData['questions'] as $questionData) {
                $question = new Question();
                $question->setText($questionData['text'])
                    ->setOptions($questionData['options'])
                    ->setCorrectAnswer($questionData['correctAnswer']) // Use correctAnswer (string)
                    ->setCorrectAnswers($questionData['correctAnswers']) // Use correctAnswers (array, can be null)
                    ->setTimeLimit($questionData['timeLimit'])
                    ->setType('multiple_choice'); // Set the type

                $quiz->addQuestion($question);
            }

            $manager->persist($quiz);
        }

        $manager->flush();
    }
}
