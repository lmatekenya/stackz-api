<?php

// src/Security/AdminAccessDeniedHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

class AdminAccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(private RouterInterface $router) {}

    public function handle(Request $request, \Symfony\Component\Security\Core\Exception\AccessDeniedException $accessDeniedException): ?\Symfony\Component\HttpFoundation\Response
    {
        // Redirect to login or another page
        return new RedirectResponse($this->router->generate('app_login'));
    }
}

