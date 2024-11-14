<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    final public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route(path: '/signin', name: 'security_signin')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastAlias = $authenticationUtils->getLastUsername();
        return $this->render('main/login.html.twig', [
                'last_alias' => $lastAlias,
                'error' => $error
    ]);
    }

    #[Route(path: '/signout', name: 'security_signout')]
    public function logout(): void
    {
        throw new \LogicException('UwU');
    }
}