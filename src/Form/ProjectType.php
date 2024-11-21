<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Student;
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
            ->add('description');


        if ($user && in_array('ROLE_TEACHER', $user->getRoles())) {
            $builder->add('proposedBy', EntityType::class, [
                'class' => Teacher::class,
                'data' => $user,
                'disabled' => true,
            ]);
        } else {
            $builder->add('proposedBy', EntityType::class, [
                'class' => Teacher::class,
                'placeholder' => 'Seleccione un profesor'
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
