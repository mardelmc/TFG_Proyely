<?php

namespace App\Controller;

use App\Entity\StudentProjectPriority;
use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\StudentProjectPriorityRepository;
use App\Repository\StudentRepository;
use Psr\Log\LoggerInterface;
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

        foreach ($students as $student) {
            $priorities[$student->getId()] = $priorityRepository->findBy(['student' => $student], ['priority' => 'ASC']);
        }

        return $this->render('class/studentsListGroup.html.twig', [
            'students' => $students,
            'priorities' => $priorities,
        ]);
    }

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

    #[Route('/student/projects', name: 'studentProjects')]
    public function studentProjects(
        ProjectRepository $projectRepository,
        StudentProjectPriorityRepository $priorityRepository
    ): Response {
        $user = $this->getUser();

        if (!$user || !in_array('ROLE_STUDENT', $user->getRoles())) {
            throw $this->createAccessDeniedException('No tienes acceso a esta página.');
        }

        $group = $user->getGroup();

        if (!$group) {
            throw $this->createNotFoundException('No tienes un grupo asignado.');
        }

        $projects = $projectRepository->findBy(['group' => $group]);
        $priorities = $priorityRepository->findBy(['student' => $user]);

        $projectPriorities = [];
        foreach ($priorities as $priority) {
            $projectPriorities[$priority->getProject()->getId()] = $priority->getPriority();
        }

        return $this->render('class/studentProject.html.twig', [
            'projects' => $projects,
            'projectPriorities' => $projectPriorities,
        ]);
    }

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

}