<?php

namespace App\Controller;

use App\Entity\StudentProjectPriority;
use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\StudentProjectPriorityRepository;
use App\Repository\StudentRepository;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassController extends AbstractController
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[IsGranted('ROLE_TUTOR')]
    #[Route('/listStudentsGroup', name: 'listStudentsGroup')]
    public function listStudentsGroup(
        GroupRepository $groupRepository,
        StudentRepository $studentRepository,
        StudentProjectPriorityRepository $priorityRepository,
        ProjectRepository $projectRepository
    ): Response {
        $user = $this->getUser();

        $students = $studentRepository->findByTutor($user);
        $priorities = [];
        $groupProjects = [];

        foreach ($students as $student) {
            $priorities[$student->getId()] = $priorityRepository->findBy(['student' => $student], ['priority' => 'ASC']);

            // Obtener proyectos del grupo del estudiante
            $group = $student->getGroup();
            if ($group) {
                $groupProjects[$group->getId()] = $projectRepository->findBy(['group' => $group]);
            }
        }

        return $this->render('class/studentsListGroup.html.twig', [
            'students' => $students,
            'priorities' => $priorities,
            'groupProjects' => $groupProjects, // Proyectos organizados por grupo
        ]);
    }


    #[IsGranted('ROLE_TUTOR')]
    #[Route('/updateStudentMark/{id}', name: 'updateStudentMark', methods: ['POST'])]
    public function updateStudentMark(Request $request, StudentRepository $studentRepository, int $id): Response
    {
        $student = $studentRepository->find($id);
        if (!$student) {
            throw $this->createNotFoundException('El estudiante no existe.');
        }

        $newMark = $request->request->get('mark');
        if ($newMark !== null) {
            $student->setMark((float)$newMark);
            $studentRepository->save($student, true);
        }

        return $this->redirectToRoute('listStudentsGroup');
    }

    #[IsGranted('ROLE_STUDENT')]
    #[Route('/studentProjectsPrioritize', name: 'studentProjects')]
    public function studentProjects(
        ProjectRepository $projectRepository,
        StudentProjectPriorityRepository $priorityRepository
    ): Response {
        $user = $this->getUser();

        $projects = [];
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $group = $user->getGroup();
            if (!$group) {
                throw $this->createNotFoundException('No tienes un grupo asignado.');
            }

            $projects = $projectRepository->findBy(['group' => $group]);
        }

        $priorities = $priorityRepository->findBy(['student' => $user]);

        $projectPriorities = [];
        foreach ($priorities as $priority) {
            $projectPriorities[$priority->getProject()->getId()] = $priority->getPriority();
        }

        // Ordenar proyectos por prioridad si existe, los demás al final
        usort($projects, function ($a, $b) use ($projectPriorities) {
            $priorityA = $projectPriorities[$a->getId()] ?? PHP_INT_MAX;
            $priorityB = $projectPriorities[$b->getId()] ?? PHP_INT_MAX;
            return $priorityA <=> $priorityB;
        });

        return $this->render('class/studentProject.html.twig', [
            'projects' => $projects,
            'projectPriorities' => $projectPriorities,
        ]);
    }


    #[IsGranted('ROLE_STUDENT')]
    #[Route('/student/projects/prioritize', name: 'prioritizeProjects', methods: ['POST'])]
    public function prioritizeProjects(
        Request $request,
        ProjectRepository $projectRepository,
        StudentProjectPriorityRepository $priorityRepository
    ): Response {
        $user = $this->getUser();
        $this->logger->info('Ordered Project IDs: ' . $request->request->get('orderedProjectIds'));

        if (!$user || !in_array('ROLE_STUDENT', $user->getRoles())) {
            throw $this->createAccessDeniedException('No tienes acceso a esta página.');
        }

        $orderedProjectIds = json_decode($request->request->get('orderedProjectIds'), true);

        if (!is_array($orderedProjectIds)) {
            $this->addFlash('error', 'Datos inválidos.');
            return $this->redirectToRoute('studentProjects');
        }

        $priority = 1; // Inicia la prioridad desde 1
        foreach ($orderedProjectIds as $projectId) {
            $project = $projectRepository->find($projectId);
            if (!$project) {
                continue;
            }

            $studentPriority = $priorityRepository->findOneBy([
                'student' => $user,
                'project' => $project,
            ]);

            if (!$studentPriority) {
                $studentPriority = new StudentProjectPriority();
                $studentPriority->setStudent($user);
                $studentPriority->setProject($project);
            }

            $studentPriority->setPriority($priority);
            $priorityRepository->save($studentPriority);
            $priority++;
        }

        $priorityRepository->flush();

        $this->addFlash('success', 'Prioridades guardadas correctamente.');
        return $this->redirectToRoute('studentProjects');
    }
    #[IsGranted('ROLE_TUTOR')]
    #[Route('/assignProjects', name: 'assignProjects', methods: ['POST'])]
    public function assignProjectsToStudents(
        StudentRepository $studentRepository,
        StudentProjectPriorityRepository $priorityRepository,
        ProjectRepository $projectRepository
    ): Response {
        $user = $this->getUser();
        $students = $studentRepository->findByTutor($user);

        // Limpiar todas las asignaciones de proyectos actuales
        foreach ($students as $student) {
            $project = $student->getProject();
            if ($project !== null) {
                $project->setStudent(null); // Desvincular proyecto del estudiante
                $student->setProject(null); // Desvincular estudiante del proyecto
                $studentRepository->add($student); // Persistir cambios
            }
        }

        $studentRepository->save(); // Guardar todos los cambios de desvinculación

        // Filtrar estudiantes con nota asignada y ordenarlos por nota descendente
        $studentsWithMarks = array_filter($students, fn($student) => $student->getMark() !== null);
        usort($studentsWithMarks, fn($a, $b) => $b->getMark() <=> $a->getMark());

        // Obtener todos los proyectos disponibles
        $availableProjects = $projectRepository->findBy(['student' => null]);

        // Mapear las prioridades de los estudiantes
        $studentPriorities = [];
        foreach ($studentsWithMarks as $student) {
            $priorities = $priorityRepository->findBy(['student' => $student], ['priority' => 'ASC']);
            $studentPriorities[$student->getId()] = $priorities;
        }

        $projectAssignments = [];
        $unassignedStudents = $studentsWithMarks; // Inicialmente, todos los estudiantes están sin asignar
        $round = 0;

        while (!empty($unassignedStudents) && !empty($availableProjects)) {
            $round++;
            $newUnassignedStudents = [];

            foreach ($unassignedStudents as $student) {
                $priorities = $studentPriorities[$student->getId()] ?? [];
                $assigned = false;

                foreach ($priorities as $priority) {
                    $project = $priority->getProject();

                    // Verificar si el proyecto está disponible
                    if (!in_array($project, $availableProjects, true)) {
                        continue;
                    }

                    // Asignar proyecto al estudiante y sincronizar
                    $student->setProject($project);
                    $project->setStudent($student);

                    // Remover el proyecto de los disponibles
                    $availableProjects = array_filter($availableProjects, fn($p) => $p !== $project);

                    $studentRepository->add($student); // Persistir estudiante
                    $assigned = true;
                    break;
                }

                if (!$assigned) {
                    $newUnassignedStudents[] = $student; // No se pudo asignar en esta ronda
                }
            }

            // Actualizar la lista de estudiantes no asignados
            $unassignedStudents = $newUnassignedStudents;

            // Log de asignaciones
            $this->logger->info("Resultados de la Ronda {$round}:");
            foreach ($studentsWithMarks as $student) {
                $assignedProject = $student->getProject();
                if ($assignedProject !== null) {
                    $this->logger->info("Estudiante {$student->getId()} ({$student->getFirstName()} {$student->getLastName()}) asignado al Proyecto {$assignedProject->getId()} ({$assignedProject->getName()}).");
                } else {
                    $this->logger->info("Estudiante {$student->getId()} ({$student->getFirstName()} {$student->getLastName()}) no tiene proyecto asignado.");
                }
            }
        }

        // Si no quedan proyectos, los estudiantes con menor nota quedan sin asignar
        if (empty($availableProjects) && !empty($unassignedStudents)) {
            $this->logger->info('Se han terminado los proyectos disponibles. Estudiantes sin asignar:');
            foreach ($unassignedStudents as $student) {
                $this->logger->info("Estudiante {$student->getId()} ({$student->getFirstName()} {$student->getLastName()}) quedó sin proyecto debido a falta de proyectos disponibles.");
            }
        }

        $studentRepository->save();
        $this->addFlash('success', 'Los proyectos han sido reasignados correctamente.');

        return $this->redirectToRoute('listStudentsGroup');
    }

    #[IsGranted('ROLE_TUTOR')]
    #[Route('/changeStudentProject/{id}', name: 'changeStudentProject', methods: ['POST'])]
    public function changeStudentProject(
        Request $request,
        StudentRepository $studentRepository,
        ProjectRepository $projectRepository,
        int $id
    ): Response {
        $student = $studentRepository->find($id);
        if (!$student) {
            throw $this->createNotFoundException('El estudiante no existe.');
        }

        $projectId = $request->request->get('projectId');
        $newProject = $projectRepository->find($projectId);

        if (!$newProject) {
            $this->addFlash('error', 'El proyecto seleccionado no existe.');
            return $this->redirectToRoute('listStudentsGroup');
        }

        $currentProject = $student->getProject();
        $currentProject?->setStudent(null);

        $student->setProject($newProject);
        $newProject->setStudent($student);

        $studentRepository->save();

        $this->addFlash('success', 'Proyecto actualizado correctamente.');
        return $this->redirectToRoute('listStudentsGroup');
    }

}