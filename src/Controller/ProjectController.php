<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Psr\Log\LoggerInterface;

class ProjectController extends AbstractController
{
public function __construct(LoggerInterface $logger)
{
    $this->logger = $logger;
}
    #[IsGranted('ROLE_TEACHER')]
    #[Route('/project', name: 'listProject')]
    public function list(
        Request $request,
        ProjectRepository $projectRepository,
        StudentRepository $studentRepository,
        TeacherRepository $teacherRepository,
        GroupRepository $groupRepository,
        PaginatorInterface $paginator
    ): Response {
        // Obtener todos los proyectos, profesores y grupos
        $projects = $projectRepository->findAll();
        $teachers = $teacherRepository->findAll();
        $groups = $groupRepository->findAll();

        // Recibir los filtros desde la solicitud
        $selectedGroup = $request->query->get('group');
        $selectedTeacher = $request->query->get('teacher');
        $isAssigned = $request->query->get('isAssigned');

        // Filtrar los proyectos según los filtros seleccionados
        $filteredProjects = array_filter($projects, function($project) use ($selectedGroup, $selectedTeacher, $isAssigned) {
            if ($selectedGroup && (!$project->getStudent() || $project->getStudent()->getGroup()->getId() != $selectedGroup)) {
                return false;
            }
            if ($selectedTeacher && (!$project->getProposedBy() || stripos($project->getProposedBy()->getFirstName() . ' ' . $project->getProposedBy()->getLastName(), $selectedTeacher) === false)) {
                return false;
            }
            if ($isAssigned !== null && (($isAssigned == 1 && !$project->getStudent()) || ($isAssigned == 0 && $project->getStudent()))) {
                return false;
            }
            return true;
        });

        // Paginación de los proyectos filtrados
        $pagination = $paginator->paginate(
            $filteredProjects,
            $request->query->getInt('page', 1), // Número de página actual
            10 // Número de elementos por página
        );

        return $this->render('project/list.html.twig', [
            'pagination' => $pagination,
            'teachers' => $teachers,
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'selectedTeacher' => $selectedTeacher,
            'isAssigned' => $isAssigned,
        ]);
    }


    #[IsGranted('ROLE_TEACHER')]
    #[Route('/project/new', name: 'newProject')]
    public function new(
        Request $request,
        ProjectRepository $projectRepository,
    ): Response
    {
        $project = new Project();
        $projectRepository->add($project);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try {
                $this->logger->info('project proposed', ['project' => $project->getProposedBy()]);
                $this->logger->info('project name', ['project' => $project->getName()]);
                //$this->logger->info('Saving project', ['project' => $project->getStudent()->getId()]);
                $this->logger->info('Saving project', ['project' => $project->getProposedBy()->getId()]);
                $projectRepository->save();
                $this->addFlash('success', 'The project has been registered succesfully');
                return $this->redirectToRoute('listProject');
            }catch (\Exception $e){
                $this->addFlash('error', 'Register could not be saved');
            }
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_TEACHER')]
    #[Route('/project/delete/{id}', name: 'deleteProject')]
    public function delete(
        Request $request,
        ProjectRepository $projectRepository,
        Project $project ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                $projectRepository->remove($project);
                $projectRepository->save();
                $this->addFlash('success', 'Proyecto eliminado con éxito');
                return $this->redirectToRoute('listProject');
            } catch (\Exception $e) {
                $this->logger->error('error deleting project', ['error' => $e->getMessage()]);
                $this->addFlash('error', 'No se ha podido eliminar el proyecto');
            }
        }
        return $this->render('project/delete.html.twig', [
            'project' => $project
        ]);
    }

    #[IsGranted('ROLE_TEACHER')]
    #[Route('/project/modify/{id}', name: 'modifyProject')]
    public function update(
        Request $request,
        ProjectRepository $projectRepository,
        Project $project
    ): Response {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
$this->logger->info('Updating project', ['project' => $project->getName()]);
                $projectRepository->save();
                $this->addFlash('success', 'The project has been updated successfully');
                return $this->redirectToRoute('listProject');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Update could not be saved');
            }
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}