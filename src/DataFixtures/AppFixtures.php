<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\Subject;
use App\Factory\ProjectFactory;
use App\Factory\StudentFactory;
use App\Factory\SubjectFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        SubjectFactory::createMany(5);
        ProjectFactory::createMany(20);
        StudentFactory::createMany(20);

        $manager->flush();
    }
}
