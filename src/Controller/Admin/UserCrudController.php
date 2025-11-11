<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            EmailField::new('email'),
            TextField::new('username'),
            IntegerField::new('level'),
            IntegerField::new('totalPoints'),
            IntegerField::new('quizzesCompleted'),
            IntegerField::new('xp'),
            IntegerField::new('currencyBalance'),
            IntegerField::new('streakCount'),
            ArrayField::new('preferences')->onlyOnDetail(),
            ArrayField::new('categoryStats')->onlyOnDetail(),
            DateTimeField::new('createdAt')->onlyOnDetail(),
        ];
    }
}
