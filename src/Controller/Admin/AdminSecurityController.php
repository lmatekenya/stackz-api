<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminSecurityController extends AbstractController
{
    #[Route('/admin/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get login error if any
        $error = $authenticationUtils->getLastAuthenticationError();
        // Get last entered username
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/admin_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/access-denied', name: 'access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }

//    #[Route('/logout', name: 'app_logout')]
//    public function logout(): void
//    {
//        throw new \LogicException('Logout is handled by Symfony.');
//    }

}
