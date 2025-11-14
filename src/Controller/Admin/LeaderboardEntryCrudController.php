<?php

namespace App\Controller\Admin;

use App\Entity\LeaderboardEntry;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LeaderboardEntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LeaderboardEntry::class;
    }

    // Optional: customize fields shown in admin
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('score'),
            IntegerField::new('rank')->onlyOnIndex(),
            IntegerField::new('timeElapsed'),
            AssociationField::new('user')->setRequired(true),
            AssociationField::new('quiz')  // <-- this ensures quiz_id is set
            ->setRequired(true)
                ->setCrudController(QuizCrudController::class),
            TextField::new('quizTitle')->hideOnForm(), // auto-filled
            DateTimeField::new('date')->setRequired(true),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof LeaderboardEntry && $entityInstance->getQuiz()) {
            // Automatically set quizTitle from the selected quiz
            $entityInstance->setQuizTitle($entityInstance->getQuiz()->getTitle());
        }

        // Call parent to actually save the entity
        parent::persistEntity($entityManager, $entityInstance);
    }
}
