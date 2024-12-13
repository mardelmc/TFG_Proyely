<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Teacher;
use App\Factory\AcademicYearFactory;
use App\Factory\GroupFactory;
use App\Factory\ProjectFactory;
use App\Factory\StudentFactory;
use App\Factory\StudentProjectPriorityFactory;
use App\Factory\SubjectFactory;
use App\Factory\TeacherFactory;
use App\Repository\TeacherRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private TeacherRepository $teacherRepository;
    public function __construct(UserPasswordHasherInterface $passwordHasher, TeacherRepository $teacherRepository, LoggerInterface $logger)
    {
        $this->passwordHasher = $passwordHasher;
        $this->teacherRepository = $teacherRepository;
        $this->logger = $logger;
    }
    public function load(ObjectManager $manager): void
    {
        $teacher = new Teacher();
        $teacher->setNickname('uwu');
        $teacher->setFirstName('Maria');
        $teacher->setLastName('Keeper');
        $teacher->setPassword(
            $this->passwordHasher->hashPassword($teacher, '1234')
        );
        $teacher->setRoles(['ROLE_TEACHER', 'ROLE_ADMIN']);
        $this->teacherRepository->add($teacher, true);

        $demoAcademicYear = AcademicYearFactory::createOne();
        $fakeTeacher = new Teacher();
        $demoTeacher = TeacherFactory::createOne([
            'nickname' => 'gregorio',
            'firstName' => 'Gregorio',
            'lastName' => 'Palomares',
            'password' => $this->passwordHasher->hashPassword($fakeTeacher, '1234'),
            'roles' => ['ROLE_TEACHER', 'ROLE_ADMIN', 'ROLE_TUTOR'],
        ]);
        $this->logger->info("Creado teacher");

        $demoSubjects = SubjectFactory::createMany(5);
        $this->logger->info("Creado Subjects");

        // Crear un grupo manualmente
        $group = new Group();
        $group->setName('2º F.P.G.S. (Desarrollo de Aplicaciones Web)');
        $group->setDescription('2º DAW');
        $group->setAcademicYear($demoAcademicYear->object());
        $group->addTutor($demoTeacher->object());
        $group->setSubjects(new ArrayCollection(array_map(fn($subject) => $subject->object(), $demoSubjects)));

        // Persistir el grupo primero para tenerlo disponible
        $manager->persist($group);
        $manager->flush();
        $this->logger->info("Creado grupos");

        $demoStudents = StudentFactory::createMany(8, [
            'group' => $group, // Asegurar que el grupo se asigna al crear los estudiantes
        ]);

        $manager->flush(); // Persistir el grupo y los estudiantes
        $this->logger->info("Creado students");

        $demoProjects = [];
        foreach ($demoSubjects as $subject) {
            $arraySubject = [$subject];
            $demoProjects = array_merge(
                $demoProjects,
                ProjectFactory::createMany(2, [
                    'subjects' => $arraySubject,
                    'proposedBy' => $demoTeacher,
                    'student' => null,
                    'group' => $group, // Asignar el grupo directamente aquí
                ])
            );
        }
        $this->logger->info("Creado Projects");

        foreach ($demoStudents as $student) {

            // Shuffle the projects to assign random unique priorities
            shuffle($demoProjects);

            $priority = 1; // Start with the highest priority
            foreach ($demoProjects as $project) {
                StudentProjectPriorityFactory::createOne([
                    'student' => $student,
                    'project' => $project->object(), // Asegurar que es un objeto
                    'priority' => $priority,
                ]);
                $priority++; // Increment priority for the next project
            }
        }

        $manager->flush();
    }

}
