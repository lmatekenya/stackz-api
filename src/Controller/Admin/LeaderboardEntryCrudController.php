<?php

namespace App\Controller\Admin;

use App\Entity\LeaderboardEntry;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            'id',
            'user',
            'score',
            'rank',
            'createdAt',
        ];
    }
}
