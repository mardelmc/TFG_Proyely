<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController
{
    #[Route('/project', name: 'project')]
    final public function list (ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }
}