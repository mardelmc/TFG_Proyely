<?php

namespace App\DataFixtures;

use App\Entity\AcademicYear;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use App\Factory\AcademicYearFactory;
use App\Factory\GroupFactory;
use App\Factory\ProjectFactory;
use App\Factory\StudentFactory;
use App\Factory\SubjectFactory;
use App\Factory\TeacherFactory;
use App\Factory\UserFactory;
use App\Repository\TeacherRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private TeacherRepository $teacherRepository;
    public function __construct(UserPasswordHasherInterface $passwordHasher, TeacherRepository $teacherRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->teacherRepository = $teacherRepository;
    }
    public function load(ObjectManager $manager): void
    {
        $academicYears = AcademicYearFactory::createMany(4);

        $teacher = new Teacher();
        $teacher->setNickname('uwu');
        $teacher->setFirstName('Maria');
        $teacher->setLastName('Keeper');
        $teacher->setPassword(
            $this->passwordHasher->hashPassword($teacher, '1234')
        );
        $teacher->setRoles(['ROLE_TEACHER', 'ROLE_ADMIN']);
        $this->teacherRepository->add($teacher, true);

        $subjects = SubjectFactory::createMany(5);

        $allStudents = [];

        foreach ($academicYears as $academicYear) {
            $groups = GroupFactory::createMany(2, [
                'academicYear' => $academicYear,
            ]);

            foreach ($groups as $group) {
                $tutors = TeacherFactory::createMany(1);
                foreach ($tutors as $tutor) {
                    $group->addTutor($tutor->object());
                }

                $students = StudentFactory::createMany(5, [
                    'group' => $group,
                ]);
                $allStudents = array_merge($allStudents, $students);
            }
        }


        foreach ($allStudents as $index => $student) {
            ProjectFactory::createOne([
                'student' => $student,
                'proposedBy' => $teacher,
            ]);
        }

        $manager->flush();
    }
}
