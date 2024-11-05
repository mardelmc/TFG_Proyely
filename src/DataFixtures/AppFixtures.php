<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\User;
use App\Factory\ProjectFactory;
use App\Factory\StudentFactory;
use App\Factory\SubjectFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        SubjectFactory::createMany(5);
        ProjectFactory::createMany(20);
        StudentFactory::createMany(20);
        UserFactory::createOne([
            'nickname' => 'uwu',
            'password' => $this->passwordHasher->hashPassword(
                new User(),
            '1234')
        ]);

        $manager->flush();
    }
}
