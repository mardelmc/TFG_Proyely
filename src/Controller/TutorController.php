<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutorController extends AbstractController
{
    #[Route('/listStudentsBy', name: 'listStudentsBy')]
    final public function list (StudentRepository $studentRepository): Response
    {
        $user = $this->getUser();

        $students = $studentRepository->findByTutor($user);

        return $this->render('user/studentsList.html.twig', [
            'students' => $students,
        ]);
    }
}