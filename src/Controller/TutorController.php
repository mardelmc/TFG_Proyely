<?php

namespace App\Controller;

use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutorController extends AbstractController
{
    /*
    #[Route('/listStudents/{idTeacher}', name: 'project')]
    final public function list (Student $studentRespository): Response
    {
        $projects = $studentRespository->findByTeacher($idTeacher);
        return $this->render('tutor/studentsList.html.twig', [
            'projects' => $projects,
        ]);
    }*/
}