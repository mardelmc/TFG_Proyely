<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use App\Factory\ProjectFactory;
use App\Factory\StudentFactory;
use App\Factory\SubjectFactory;
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
        // $product = new Product();
        // $manager->persist($product);
        SubjectFactory::createMany(5);
        ProjectFactory::createMany(20);
        StudentFactory::createMany(20);
        $teacher = new Teacher();
        $teacher->setNickname('uwu');
        $teacher->setFirstName('Maria');
        $teacher->setLastName('Keeper');
        $teacher->setTutor(true);
        $teacher->setPassword(
            $this->passwordHasher->hashPassword($teacher, '1234')
        );

        $teacher->setRoles(['ROLE_TEACHER', 'ROLE_ADMIN']);

        $this->teacherRepository->add($teacher, true);

        $manager->flush();
    }
}
