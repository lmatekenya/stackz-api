<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Quiz;
use App\Entity\LeaderboardEntry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('StackZ Administration')
            ->setFaviconPath('favicon.ico')
            ->setTextDirection('ltr');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Quizzes', 'fas fa-question-circle', Quiz::class);
        yield MenuItem::linkToCrud('Leaderboard', 'fas fa-trophy', LeaderboardEntry::class);
        yield MenuItem::linkToUrl('API Docs', 'fas fa-book', '/api/docs');
        yield MenuItem::linkToUrl('Back to Site', 'fas fa-globe', '/');
    }
}
