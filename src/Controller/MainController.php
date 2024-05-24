<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    final public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route(path: '/signin', name: 'security_signin')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route(path: '/signout', name: 'security_signout')]
    public function logout(): void
    {
        throw new \LogicException('UwU');
    }
}