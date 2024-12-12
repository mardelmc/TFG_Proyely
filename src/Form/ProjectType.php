<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('name')
            ->add('description')
            ->add('group')
            ->add('subjects', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name', // Muestra el nombre del Subject
                'multiple' => true, // Permite seleccionar múltiples Subjects
                'expanded' => true, // Usa checkboxes para seleccionar
                'required' => false,
                'placeholder' => 'Seleccione un módulo', // Agrega un placeholder opcional
            ])
        ;
        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            $builder->add('proposedBy', EntityType::class, [
                'class' => Teacher::class,
                'placeholder' => 'Seleccione un profesor',
            ]);
        } else {
            $builder->add('proposedBy', EntityType::class, [
                'class' => Teacher::class,
                'data' => $user,
                'attr' => ['readonly' => true],
            ]);}
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}