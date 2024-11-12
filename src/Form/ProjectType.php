<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'query_builder' => function (StudentRepository $studentRepository) {
                    return $studentRepository->createQueryBuilder('s')
                        ->leftJoin('s.project', 'p')
                        ->where('p.id IS NULL');
                }
            ])
            ->add('proposedBy');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
