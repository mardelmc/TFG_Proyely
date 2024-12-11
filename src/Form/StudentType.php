<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            ->add('nickname')
            ->add('password', PasswordType::class, [
                'required' => $options['is_new'],
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('mark')
            ->add('group')
            ->add('project')
            ->add('subjects')
        ;

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
