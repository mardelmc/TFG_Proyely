<?php
namespace App\Entity\Validators;

use App\Entity\Group;
use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueTutorPerAcademicYearValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Teacher) {
            return;
        }

        foreach ($value->getTutoredGroups() as $group) {
            $academicYear = $group->getAcademicYear();

            $existingGroup = $this->entityManager->getRepository(Group::class)
                ->findOneBy(['tutor' => $value, 'academicYear' => $academicYear]);

            if ($existingGroup && $existingGroup !== $group) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ teacher }}', $value->getFirstName() . ' ' . $value->getLastName())
                    ->setParameter('{{ year }}', $academicYear->getName())
                    ->addViolation();
            }
        }
    }
}
