<?php

namespace App\Entity\Validators;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute(Attribute::TARGET_CLASS)]
class UniqueTutorPerAcademicYear extends Constraint
{
    public string $message = 'The teacher "{{ teacher }}" is already assigned to a group in the academic year "{{ year }}".';
}