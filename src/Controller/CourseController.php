<?php

namespace App\Controller;

use App\Entity\AcademicYear;
use App\Entity\Group;
use App\Entity\Subject;
use App\Form\AcademicYearType;
use App\Form\GroupType;
use App\Form\SubjectType;
use App\Repository\AcademicYearRepository;
use App\Repository\GroupRepository;
use App\Repository\SubjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    //Groups
    #[Route('/listGroups', name: 'listGroups')]
    public function listGroups(
        Request $request,
        GroupRepository $groupRepository,
        AcademicYearRepository $academicYearRepository,
        PaginatorInterface $paginator
    ): Response {
        $selectedAcademicYear = $request->query->get('academicYear');
        $selectedGroupName = $request->query->get('groupName');

        $groupsQuery = $groupRepository->findGroupsWithFilters($selectedAcademicYear, $selectedGroupName);

        $pagination = $paginator->paginate(
            $groupsQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('course/groupList.html.twig', [
            'pagination' => $pagination,
            'selectedAcademicYear' => $selectedAcademicYear,
            'selectedGroupName' => $selectedGroupName,
            'academicYears' => $academicYearRepository->findAll(),
        ]);
    }

    #[Route('/listGroups/new', name: 'newGroup')]
    public function newGroup(
        Request $request,
        GroupRepository $groupRepository
    ): Response
    {

        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $groupRepository->add($group);
                $this->addFlash('success', 'El grupo ha sido añadido.');
                return $this->redirectToRoute('listGroups');
            } catch (\Exception $e) {
                $this->addFlash('error', 'El grupo no se ha guardado. Error: ' . $e->getMessage());
            }
        }

        return $this->render('course/groupModify.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listGroups/modify/{id}', name: 'modifyGroup')]
    public function modifyGroup(
        Request $request,
        GroupRepository $groupRepository,
        Group $group
    ): Response {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $groupRepository->save();
                $this->addFlash('success', 'El grupo ha sido modificado con éxito.');
                return $this->redirectToRoute('listGroups');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo modificar el grupo. Error: ' . $e->getMessage());
            }
        }

        return $this->render('course/groupModify.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/listGroups/delete/{id}', name: 'deleteGroup')]
    final public function deleteTeacher(
        GroupRepository $groupRepository,
        Group $group,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $groupRepository->remove($group);
                $groupRepository->save();
                $this->addFlash('success', 'El grupo ha sido eliminado con éxito');
                return $this->redirectToRoute('listGroups');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar el grupo. Error: ' . $e);
            }
        }

        return $this->render('course/groupDelete.html.twig', [
            'group' => $group
        ]);
    }

    //Subjects
    #[Route('/listSubjects', name: 'listSubjects')]
    public function listSubjects(
        Request $request,
        SubjectRepository $subjectRepository,
        PaginatorInterface $paginator
    ): Response {
        $selectedSubjectName = $request->query->get('subjectName');

        $subjectsQuery = $subjectRepository->findSubjectsWithFilters($selectedSubjectName);

        $pagination = $paginator->paginate(
            $subjectsQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('course/subjectsList.html.twig', [
            'pagination' => $pagination,
            'selectedSubjectName' => $selectedSubjectName,
        ]);
    }

    #[Route('/listSubjects/new', name: 'newSubject')]
    public function newSubject(
        Request $request,
        SubjectRepository $subjectRepository
    ): Response
    {

        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $subjectRepository->add($subject);
                $this->addFlash('success', 'El módulo ha sido añadido.');
                return $this->redirectToRoute('listSubjects');
            } catch (\Exception $e) {
                $this->addFlash('error', 'El módulo no se ha guardado. Error: ' . $e->getMessage());
            }
        }

        return $this->render('course/subjectModify.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listSubjects/modify/{id}', name: 'modifySubject')]
    public function modifySubject(
        Request $request,
        SubjectRepository $subjectRepository,
        Subject $subject
    ): Response {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $subjectRepository->save();
                $this->addFlash('success', 'El módulo ha sido modificado con éxito.');
                return $this->redirectToRoute('listSubjects');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo modificar el módulo. Error: ' . $e->getMessage());
            }
        }

        return $this->render('course/subjectModify.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listSubjects/delete/{id}', name: 'deleteSubject')]
    final public function deleteSubject(
        SubjectRepository $subjectRepository,
        Subject $subject,
        Request $request
    ): Response
    {
        if ($request->request->has('confirmar')) {
            try{
                $subjectRepository->remove($subject);
                $subjectRepository->save();
                $this->addFlash('success', 'El módulo ha sido eliminado con éxito');
                return $this->redirectToRoute('listSubjects');
            }catch (\Exception $e){
                $this->addFlash('error', 'No se ha podido eliminar el módulo. Error: ' . $e);
            }
        }

        return $this->render('course/subjectDelete.html.twig', [
            'subject' => $subject
        ]);
    }

    #[Route('/listAcademicYears', name: 'listAcademicYears')]
    public function listAcademicYears(
        Request $request,
        AcademicYearRepository $academicYearRepository,
        PaginatorInterface $paginator
    ): Response {
        $selectedDescription = $request->query->get('description');

        $academicYearsQuery = $academicYearRepository->findAcademicYearsWithFilters($selectedDescription);

        $pagination = $paginator->paginate(
            $academicYearsQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('course/academicYearsList.html.twig', [
            'pagination' => $pagination,
            'selectedDescription' => $selectedDescription,
        ]);
    }

    #[Route('/listAcademicYears/new', name: 'newAcademicYear')]
    public function newAcademicYear(
        Request $request,
        AcademicYearRepository $academicYearRepository
    ): Response {
        $academicYear = new AcademicYear();
        $form = $this->createForm(AcademicYearType::class, $academicYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $academicYearRepository->add($academicYear);
                $this->addFlash('success', 'El año académico ha sido añadido.');
                return $this->redirectToRoute('listAcademicYears');
            } catch (\Exception $e) {
                $this->addFlash('error', 'El año académico no se ha guardado. Error: ' . $e->getMessage());
            }
        }

        return $this->render('course/academicYearModify.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listAcademicYears/modify/{id}', name: 'modifyAcademicYear')]
    public function modifyAcademicYear(
        Request $request,
        AcademicYearRepository $academicYearRepository,
        AcademicYear $academicYear
    ): Response {
        $form = $this->createForm(AcademicYearType::class, $academicYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $academicYearRepository->save();
                $this->addFlash('success', 'El año académico ha sido modificado con éxito.');
                return $this->redirectToRoute('listAcademicYears');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo modificar el año académico. Error: ' . $e->getMessage());
            }
        }

        return $this->render('course/academicYearModify.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listAcademicYears/delete/{id}', name: 'deleteAcademicYear')]
    public function deleteAcademicYear(
        AcademicYearRepository $academicYearRepository,
        AcademicYear $academicYear,
        Request $request
    ): Response {
        if ($request->request->has('confirmar')) {
            try {
                $academicYearRepository->remove($academicYear);
                $academicYearRepository->save();
                $this->addFlash('success', 'El año académico ha sido eliminado con éxito.');
                return $this->redirectToRoute('listAcademicYears');
            } catch (\Exception $e) {
                $this->addFlash('error', 'No se pudo eliminar el año académico. Error: ' . $e->getMessage());
            }
        }

        return $this->render('course/academicYearDelete.html.twig', [
            'academicYear' => $academicYear,
        ]);
    }

}