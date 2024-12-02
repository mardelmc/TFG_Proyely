<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{

    //Students
    #[Route('/listStudents', name: 'listStudents')]
    final public function listStudents (StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAll();

        return $this->render('user/studentsList.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/listStudents/modify/{id}', name: 'modifyStudent')]
    final public function modifyStudents (
        Request $request,
        StudentRepository $studentRepository,
        Student $student,
    ): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $studentRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se han podido aplicar las modificaciones');
            }
        }
        return $this->render('user/studentModify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/listStudents/delete/{id}', name: 'deleteStudent')]
    final public function deleteStudent(
        Student $student,
        StudentRepository $studentRepository,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $studentRepository->remove($student);
                $studentRepository->save();
                $this->addFlash('success', 'El estudiante ha sido eliminado con éxito');
                return $this->redirectToRoute('');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar al estudiante. Error: ' . $e);
            }
        }

        return $this->render('users/studentDelete.html.twig', [
            'user' => $student
        ]);
    }


    //Teachers
    #[Route('/listTeachers', name: 'listTeachers')]
    final public function listTeachers (TeacherRepository $teacherRepository): Response
    {
        $teachers = $teacherRepository->findAll();

        return $this->render('user/teachersList.html.twig', [
            'teachers' => $teachers,
        ]);
    }

    #[Route('/listTeachers/modify/{id}', name: 'modifyTeacher')]
    final public function modifyTeacher (
        Request $request,
        TeacherRepository $teacherRepository,
        Teacher $teacher,
    ): Response
    {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $teacherRepository->save();
                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se han podido aplicar las modificaciones');
            }
        }
        return $this->render('user/teacherModify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/listTeachers/delete/{id}', name: 'deleteTeacher')]
    final public function deleteTeacher(
        TeacherRepository $teacherRepository,
        Teacher $teacher,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $teacherRepository->remove($teacher);
                $teacherRepository->save();
                $this->addFlash('success', 'El profesor ha sido eliminado con éxito');
                return $this->redirectToRoute('');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar al profesor. Error: ' . $e);
            }
        }

        return $this->render('user/teacherDelete.html.twig', [
            'user' => $teacher
        ]);
    }
}