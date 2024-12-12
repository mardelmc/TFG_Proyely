<?php

namespace App\Factory;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Group>
 *
 * @method        Group|Proxy                     create(array|callable $attributes = [])
 * @method static Group|Proxy                     createOne(array $attributes = [])
 * @method static Group|Proxy                     find(object|array|mixed $criteria)
 * @method static Group|Proxy                     findOrCreate(array $attributes)
 * @method static Group|Proxy                     first(string $sortedField = 'id')
 * @method static Group|Proxy                     last(string $sortedField = 'id')
 * @method static Group|Proxy                     random(array $attributes = [])
 * @method static Group|Proxy                     randomOrCreate(array $attributes = [])
 * @method static GroupRepository|RepositoryProxy repository()
 * @method static Group[]|Proxy[]                 all()
 * @method static Group[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Group[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Group[]|Proxy[]                 findBy(array $attributes)
 * @method static Group[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Group[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class GroupFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $gradosSuperiores = [
            'Desarrollo de Aplicaciones Multiplataforma',
            'Desarrollo de Aplicaciones Web',
            'Administración de Sistemas Informáticos en Red',
            'Gestión de Ventas y Espacios Comerciales',
            'Laboratorio de Análisis y de Control de Calidad',
            'Marketing y Publicidad',
            'Automatización y Robótica Industrial',
            'Integración Social',
            'Educación Infantil',
            'Animaciones 3D, Juegos y Entornos Interactivos',
        ];

        return [
            'academicYear' => AcademicYearFactory::randomOrCreate(),
            'description' => self::faker()->text(255),
            'name' => sprintf(
                '%sº F.P.G.S. (%s)',
                self::faker()->numberBetween(1, 2),
                self::faker()->randomElement($gradosSuperiores)
            ),
        ];
    }

    protected function initialize(): self
    {
        return $this->afterInstantiate(function (Group $group): void {
            // Crear un tutor para el grupo
            $tutor = TeacherFactory::new()->create();
            $group->addTutor($tutor->object());

            // Asignar el rol de tutor al profesor
            $tutor->addRole('ROLE_TUTOR');

            // Crear dos subjects para el grupo
            $subject1 = SubjectFactory::createOne();
            $subject2 = SubjectFactory::createOne();

            // Crear estudiantes asociados al grupo
            $students = StudentFactory::createMany(5, [
                'group' => $group,
            ]);

            // Crear 4 proyectos para el grupo, 2 para cada subject
            foreach (range(1, 4) as $index) {
                $subject = $index <= 2 ? $subject1 : $subject2; // Alternar entre los subjects
                $student = $students[$index - 1]->object(); // Asignar estudiante a cada proyecto

                $project = ProjectFactory::createOne([
                    'group' => $group,
                    'student' => $student,
                    'proposedBy' => $tutor->object(), // El tutor como proponente
                ]);

                // Asignar 1 o 2 subjects al proyecto de forma aleatoria
                $assignedSubjects = [$subject1];
                if (random_int(0, 1)) {
                    $assignedSubjects[] = $subject2; // Posiblemente asignar el segundo subject
                }

                foreach ($assignedSubjects as $subject) {
                    $project->addSubject($subject->object());
                }
            }
        });
    }

    protected static function getClass(): string
    {
        return Group::class;
    }
}
