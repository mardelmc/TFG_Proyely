<?php

namespace App\Factory;

use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Teacher>
 *
 * @method        Teacher|Proxy                     create(array|callable $attributes = [])
 * @method static Teacher|Proxy                     createOne(array $attributes = [])
 * @method static Teacher|Proxy                     find(object|array|mixed $criteria)
 * @method static Teacher|Proxy                     findOrCreate(array $attributes)
 * @method static Teacher|Proxy                     first(string $sortedField = 'id')
 * @method static Teacher|Proxy                     last(string $sortedField = 'id')
 * @method static Teacher|Proxy                     random(array $attributes = [])
 * @method static Teacher|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TeacherRepository|RepositoryProxy repository()
 * @method static Teacher[]|Proxy[]                 all()
 * @method static Teacher[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Teacher[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Teacher[]|Proxy[]                 findBy(array $attributes)
 * @method static Teacher[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Teacher[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TeacherFactory extends ModelFactory
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
            'firstName' => self::faker()->text(255),
            'lastName' => self::faker()->text(255),
            'tutor' => self::faker()->boolean(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Teacher $teacher): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Teacher::class;
    }
}
