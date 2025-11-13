<?php

//namespace App\Controller\Admin;
//
//use App\Entity\User;
//use App\Entity\Quiz;
//use App\Entity\LeaderboardEntry;
//use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
//use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
//use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
//use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//
//class DashboardController extends AbstractDashboardController
//{
//    #[Route('/admin', name: 'admin')]
//    public function index(): Response
//    {
//        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
//        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
//    }
//
//    public function configureDashboard(): Dashboard
//    {
//        return Dashboard::new()
//            ->setTitle('StackZ Administration')
//            ->setFaviconPath('favicon.ico')
//            ->setTextDirection('ltr');
//    }
//
//    public function configureMenuItems(): iterable
//    {
//        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
//        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
//        yield MenuItem::linkToCrud('Quizzes', 'fas fa-question-circle', Quiz::class);
//        yield MenuItem::linkToCrud('Leaderboard', 'fas fa-trophy', LeaderboardEntry::class);
//        yield MenuItem::linkToUrl('API Docs', 'fas fa-book', '/api/docs');
//        yield MenuItem::linkToUrl('Back to Site', 'fas fa-globe', '/');
//    }
//}


namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Quiz;
use App\Entity\LeaderboardEntry;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Totals
        $totalUsers = $this->em->getRepository(User::class)->count([]);
        $totalQuizzes = $this->em->getRepository(Quiz::class)->count([]);
        $totalLeaderboardEntries = $this->em->getRepository(LeaderboardEntry::class)->count([]);

        $conn = $this->em->getConnection();

        // Monthly user stats
        $monthlyUserStats = $conn->executeQuery("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(id) AS total
        FROM users
        GROUP BY month
        ORDER BY month ASC
    ")->fetchAllAssociative();

        // Quiz stats per category
        $quizStats = $conn->executeQuery("
        SELECT COALESCE(category, 'Uncategorized') AS category, COUNT(id) AS total
        FROM quiz
        GROUP BY category
    ")->fetchAllAssociative();

        // Top 5 leaderboard entries
        $topScores = $conn->executeQuery("
        SELECT u.username, l.score
        FROM leaderboard_entry l
        JOIN users u ON l.user_id = u.id
        ORDER BY l.score DESC
        LIMIT 5
    ")->fetchAllAssociative();

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalQuizzes' => $totalQuizzes,
            'totalLeaderboardEntries' => $totalLeaderboardEntries,
            'monthlyUserStats' => $monthlyUserStats,
            'quizStats' => $quizStats,
            'topScores' => $topScores,
        ]);
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
