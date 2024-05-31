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
        return [
            'academicYear' => AcademicYearFactory::new(),
            'description' => self::faker()->text(255),
            'name' => self::faker()->text(255),
            'tutor' => TeacherFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Group $group): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Group::class;
    }
}
