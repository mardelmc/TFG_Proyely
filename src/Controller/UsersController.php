<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Form\StudentType;
use App\Form\TeacherType;
use App\Repository\AcademicYearRepository;
use App\Repository\GroupRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    //Students
    #[Route('/listStudents', name: 'listStudents')]
    final public function listStudents (
        StudentRepository $studentRepository,
        AcademicYearRepository $academicYearRepository,
    ): Response
    {
        $students = $studentRepository->findAll();
        $academicYear = $academicYearRepository->findAll();

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

        return $this->render('user/studentDelete.html.twig', [
            'user' => $student
        ]);
    }


    //Teachers
    #[Route('/listTeachers', name: 'listTeachers')]
    final public function listTeachers(
        Request $request,
        TeacherRepository $teacherRepository,
        GroupRepository $groupRepository,
        AcademicYearRepository $academicYearRepository,
        PaginatorInterface $paginator
    ): Response {
        $selectedAcademicYear = $request->query->get('academicYear');
        $selectedTeacher = $request->query->get('teacherName');
        $selectedGroup = $request->query->get('group');

        $teachersQuery = $teacherRepository->findTeachersWithFilters($selectedAcademicYear, $selectedTeacher, $selectedGroup);

        $allTeachers = $teacherRepository->findAll();

        $pagination = $paginator->paginate(
            $teachersQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/teachersList.html.twig', [
            'pagination' => $pagination,
            'selectedAcademicYear' => $selectedAcademicYear,
            'selectedTeacher' => $selectedTeacher,
            'selectedGroup' => $selectedGroup,
            'academicYears' => $academicYearRepository->findAll(),
            'groups' => $groupRepository->findAll(),
            'teachers' => $allTeachers,
        ]);
    }

    #[Route('/listTeachers/new', name: 'newTeacher')]
    public function new(
        Request $request,
        TeacherRepository $teacherRepository,
        GroupRepository $groupRepository,
    ): Response
    {
        $this->logger->info('Saving Teacher',[$request->request->all()]);
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);
        $isTutor = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $teacher->addRole('ROLE_USER');

            foreach ($teacher->getGroups() as $group) {
                $group->addTutor($teacher);
                $isTutor = true;
            }

            if ($isTutor) {
                $teacher->addRole('ROLE_TUTOR');
            }

            try {
                $teacherRepository->save($teacher, true);
                $this->logger->info('Teacher saved');
                $this->addFlash('success', 'El docente ha sido añadido.');
                return $this->redirectToRoute('listTeachers');
            } catch (\Exception $e) {
                $this->addFlash('error', 'El docente no se ha guardado. Error: ' . $e->getMessage());
            }
        }

        return $this->render('user/teacherModify.html.twig', [
            'form' => $form->createView(),
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
                return $this->redirectToRoute('listTeachers');
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
                return $this->redirectToRoute('listTeachers');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar al profesor. Error: ' . $e);
            }
        }

        return $this->render('user/teacherDelete.html.twig', [
            'teacher' => $teacher
        ]);
    }
}