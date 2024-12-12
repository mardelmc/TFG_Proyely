<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Project;
use App\Entity\Student;
use App\Entity\Subject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;

class StudentType extends AbstractType
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class, [
                'label' => 'Nombre de usuario',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'required' => $options['is_new'],
                'constraints' => $options['is_new'] ? [new NotBlank(['message' => 'La contraseña es obligatoria.'])] : [],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Apellido',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('mark', IntegerType::class, [
                'label' => 'Nota',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 10,
                        'notInRangeMessage' => 'La nota debe estar entre {{ min }} y {{ max }}.',
                    ]),
                ],
                'required' => false,
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'label' => 'Grupo',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'label' => 'Proyecto',
                'required' => false,
                'placeholder' => 'Seleccione un proyecto (opcional)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('subjects', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'name',
                'label' => 'Asignaturas',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Student $student */
            $student = $event->getData();

            if ($student->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($student, $student->getPassword());
                $student->setPassword($hashedPassword);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
            'is_new' => true,
        ]);
    }
}
