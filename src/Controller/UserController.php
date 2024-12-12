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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    //Students
    #[Route('/listStudents', name: 'listStudents')]
    final public function listStudents(
        Request $request,
        StudentRepository $studentRepository,
        GroupRepository $groupRepository,
        AcademicYearRepository $academicYearRepository,
        PaginatorInterface $paginator
    ): Response {
        $selectedAcademicYear = $request->query->get('academicYear');
        $selectedStudent = $request->query->get('studentName');
        $selectedGroup = $request->query->get('group');

        $studentsQuery = $studentRepository->findStudentsWithFilters($selectedAcademicYear, $selectedStudent, $selectedGroup);

        $pagination = $paginator->paginate(
            $studentsQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/studentsList.html.twig', [
            'pagination' => $pagination,
            'selectedAcademicYear' => $selectedAcademicYear,
            'selectedStudent' => $selectedStudent,
            'selectedGroup' => $selectedGroup,
            'academicYears' => $academicYearRepository->findAll(),
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/listStudents/new', name: 'newStudent')]
    public function newStudent(
        Request $request,
        StudentRepository $studentRepository,
        GroupRepository $groupRepository,
    ): Response
    {
        $this->logger->info('Saving Student',[$request->request->all()]);
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);

        if ($form->isSubmitted() && $form->isValid()) {
            $student->addRole('ROLE_USER');

            try {
                $studentRepository->save($student, true);
                $this->addFlash('success', 'El alumno ha sido añadido.');
                return $this->redirectToRoute('listStudents');
            } catch (\Exception $e) {
                $this->addFlash('error', 'El alumno no se ha guardado. Error: ' . $e->getMessage());
            }
        }

        return $this->render('user/studentModify.html.twig', [
            'form' => $form->createView(),
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
                return $this->redirectToRoute('listStudents');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error:' .  $e->getMessage());
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
    public function newTeacher(
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
                $teacherRepository->add($teacher);
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
    final public function modifyTeacher(
        Request $request,
        TeacherRepository $teacherRepository,
        GroupRepository $groupRepository,
        Teacher $teacher,
    ): Response {
        $this->logger->info('Updating Teacher', [$request->request->all()]);

        $originalGroups = new ArrayCollection($teacher->getGroups()->toArray());
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $newGroups = $teacher->getGroups();

                // Eliminar el docente como tutor de los grupos que ya no tiene
                foreach ($originalGroups as $originalGroup) {
                    if (!$newGroups->contains($originalGroup)) {
                        $originalGroup->removeTutor($teacher);
                        $groupRepository->save($originalGroup);
                    }
                }

                // Agregar el docente como tutor de los nuevos grupos
                foreach ($newGroups as $group) {
                    if (!$originalGroups->contains($group)) {
                        $group->addTutor($teacher);
                        $groupRepository->save($group);
                    }
                }

                // Verificar si el docente es tutor y actualizar roles
                if ($teacher->isTutor()) {
                    $teacher->addRole('ROLE_TUTOR');
                } else {
                    $teacher->setRoles(array_filter(
                        $teacher->getRoles(),
                        fn($role) => $role !== 'ROLE_TUTOR'
                    ));
                }

                // Guardar cambios en el docente
                $teacherRepository->save($teacher);

                $this->addFlash('success', 'La modificación se ha realizado correctamente');
                return $this->redirectToRoute('listTeachers');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido aplicar las modificaciones. Error: ' . $e->getMessage());
            }
        }

        return $this->render('user/teacherModify.html.twig', [
            'form' => $form->createView(),
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
                foreach ($teacher->getGroups() as $group) {
                    $group->removeTutor($teacher);
                }
                foreach ($teacher->getProjects() as $project) {
                    $project->setProposedBy(null);
                }
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