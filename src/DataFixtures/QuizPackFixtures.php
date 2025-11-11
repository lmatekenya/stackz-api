<?php
// src/DataFixtures/QuizPackFixtures.php
namespace App\DataFixtures;

use App\Entity\QuizPack;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuizPackFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $quizPack = new QuizPack();
            $quizPack->setName("Quiz Pack {$i}");
            $quizPack->setCreatedAt(new \DateTimeImmutable());
            $quizPack->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($quizPack);

            // Add reference
            $this->addReference('quizpack_' . $i, $quizPack);
        }

        $manager->flush();
    }
}
