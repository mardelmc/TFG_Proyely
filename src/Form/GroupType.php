<?php

namespace App\Form;

use App\Entity\AcademicYear;
use App\Entity\Group;
use App\Entity\Subject;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre del grupo',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('subjects', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'label' => 'Asignaturas',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tutors', EntityType::class, [
                'class' => Teacher::class,
                'choice_label' => 'firstName',
                'label' => 'Tutores',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('academicYear', EntityType::class, [
                'class' => AcademicYear::class,
                'choice_label' => 'description',
                'label' => 'Año académico',
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}