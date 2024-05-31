<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'project')]
    final public function list (ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/new', name: 'project_new')]
    public function new(
        Request $request,
        ProjectRepository $projectRepository
    )
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