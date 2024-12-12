<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Project;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProjectType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre del proyecto',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'label' => 'Grupo',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('subjects', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'label' => 'Asignaturas',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Seleccione un módulo',
            ]);

        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            $builder->add('proposedBy', EntityType::class, [
                'class' => Teacher::class,
                'choice_label' => 'firstName',
                'label' => 'Propuesto por',
                'placeholder' => 'Seleccione un profesor',
                'attr' => ['class' => 'form-control'],
            ]);
        } else {
            $builder->add('proposedBy', EntityType::class, [
                'class' => Teacher::class,
                'choice_label' => 'firstName',
                'data' => $user,
                'label' => 'Propuesto por',
                'attr' => ['readonly' => true, 'class' => 'form-control'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
