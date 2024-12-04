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
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
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
            'academicYear' => AcademicYearFactory::new(),
            'description' => self::faker()->text(255),
            'name' => sprintf(
                '%sº F.P.G.S. (%s)',
                self::faker()->numberBetween(1, 2),
                self::faker()->randomElement($gradosSuperiores)
            ),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this->afterInstantiate(function (Group $group): void {
            // Asignar tutores al grupo usando el método addTutor()
            $tutors = TeacherFactory::new()->many(1, 3)->create();
            foreach ($tutors as $tutor) {
                $group->addTutor($tutor->object());
            }
        });
    }

    protected static function getClass(): string
    {
        return Group::class;
    }
}
