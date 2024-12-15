<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TeacherType extends AbstractType
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
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'label' => 'Grupos',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('isAdmin', CheckboxType::class, [
                'label' => 'Asignar rol de administrador',
                'mapped' => false, // No mapeamos directamente en la entidad
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $teacher = $event->getData();
            $form = $event->getForm();

            if ($teacher->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($teacher, $teacher->getPassword());
                $teacher->setPassword($hashedPassword);
            }

            $isAdmin = $form->get('isAdmin')->getData();
            $roles = $teacher->getRoles();

            if ($isAdmin && !in_array('ROLE_ADMIN', $roles, true)) {
                $roles[] = 'ROLE_ADMIN';
            } elseif (!$isAdmin && in_array('ROLE_ADMIN', $roles, true)) {
                $roles = array_filter($roles, fn($role) => $role !== 'ROLE_ADMIN');
            }

            $teacher->setRoles($roles);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
            'is_new' => true,
        ]);
    }
}
