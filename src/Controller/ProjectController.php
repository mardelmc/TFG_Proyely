<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Psr\Log\LoggerInterface;

class ProjectController extends AbstractController
{
public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
{
    $this->logger = $logger;
}
    #[Route('/project', name: 'project')]
    final public function list (ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->listAll();
        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/new', name: 'project_new')]
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
$this->logger->info('Saving project', ['project' => $project->getName()]);
$this->logger->info('Saving project', ['project' => $project->getStudent()->getId()]);
$this->logger->info('Saving project', ['project' => $project->getProposedBy()->getId()]);
                $projectRepository->save();
                $this->addFlash('success', 'The project has been registered succesfully');
                return $this->redirectToRoute('project');
            }catch (\Exception $e){
                $this->addFlash('error', 'Register could not be saved');
            }
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /*
    #[Route('/project/new', name: 'project_new')]
    public function new(
        Request $request,
        ProjectRepository $projectRepository
    ): Response
    {
        $project = new Project();
        $projectRepository->add($project);
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        $nuevo = $project->getId() === null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $projectRepository->save();
                if ($nuevo) {
                    $this->addFlash('success', 'Proyecto creado con éxito');
                } else {
                    $this->addFlash('success', 'Cambios guardados con éxito');
                }
                return $this->redirectToRoute('project');
            }catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }
*/
    /*
    #[Route('/magazine/{serial}', name: 'project_edit')]
    public function edit(
        Request $request,
        MagazineRepository $magazineRepository,
        Magazine $magazine ): Response
    {
        $form = $this->createForm(MagazineType::class, $magazine);
        $form->handleRequest($request);

        $nuevo = $magazine->getId() === null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $magazineRepository->save();
                if ($nuevo) {
                    $this->addFlash('success', 'Revista creada con éxito');
                } else {
                    $this->addFlash('success', 'Cambios guardados con éxito');
                }
                return $this->redirectToRoute('magazine_list');
            }catch (\Exception $e) {
                $this->addFlash('error', 'No se han podido guardar los cambios');
            }
        }
        return $this->render('magazine/modificar.html.twig', [
            'form' => $form->createView(),
            'magazine' => $magazine
        ]);
    }
    #[Route('/magazine/delete/{serial}', name: 'magazine_delete')]
    public function delete(
        Request $request,
        MagazineRepository $magazineRepository,
        Magazine $magazine ): Response
    {
        if ($request->request->has('confirmar')) {
            try {
                $magazineRepository->remove($magazine);
                $magazineRepository->save();
                $this->addFlash('success', 'Revista eliminada con éxito');
                return $this->redirectToRoute('magazine_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se ha podido eliminar la revista');
            }
        }
        return $this->render('magazine/delete.html.twig', [
            'magazine' => $magazine
        ]);
    }*/

}