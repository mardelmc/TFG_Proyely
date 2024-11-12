<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Menu extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    final public function menu (): Response
    {
        return $this->render('main/menu.html.twig');
    }
}