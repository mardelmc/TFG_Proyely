<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutorController extends AbstractController
{

    #[Route('/listStudents', name: 'listStudents')]
    final public function list (StudentRepository $studentRepository): Response
    {
        $user = $this->getUser();
     /*   if (!$user || !in_array('ROLE_TUTOR', $user->getRoles())) {
            throw $this->createAccessDeniedException('No tienes acceso a esta página.');
        }*/

        $students = $studentRepository->findByTutor($user);

        return $this->render('tutor/studentsList.html.twig', [
            'students' => $students,
        ]);
    }
}