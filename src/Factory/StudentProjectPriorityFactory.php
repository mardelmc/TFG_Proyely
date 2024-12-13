<?php

namespace App\Factory;

use App\Entity\StudentProjectPriority;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<StudentProjectPriority>
 *
 * @method        StudentProjectPriority|Proxy              create(array|callable $attributes = [])
 * @method static StudentProjectPriority|Proxy              createOne(array $attributes = [])
 * @method static StudentProjectPriority|Proxy              find(object|array|mixed $criteria)
 * @method static StudentProjectPriority|Proxy              findOrCreate(array $attributes)
 * @method static StudentProjectPriority|Proxy              first(string $sortedField = 'id')
 * @method static StudentProjectPriority|Proxy              last(string $sortedField = 'id')
 * @method static StudentProjectPriority|Proxy              random(array $attributes = [])
 * @method static StudentProjectPriority|Proxy              randomOrCreate(array $attributes = [])
 * @method static EntityRepository|ProxyRepositoryDecorator repository()
 * @method static StudentProjectPriority[]|Proxy[]          all()
 * @method static StudentProjectPriority[]|Proxy[]          createMany(int $number, array|callable $attributes = [])
 * @method static StudentProjectPriority[]|Proxy[]          createSequence(iterable|callable $sequence)
 * @method static StudentProjectPriority[]|Proxy[]          findBy(array $attributes)
 * @method static StudentProjectPriority[]|Proxy[]          randomRange(int $min, int $max, array $attributes = [])
 * @method static StudentProjectPriority[]|Proxy[]          randomSet(int $number, array $attributes = [])
 */
final class StudentProjectPriorityFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return StudentProjectPriority::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'priority' => self::faker()->randomNumber(),
            'project' => ProjectFactory::new(),
            'student' => StudentFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(StudentProjectPriority $studentProjectPriority): void {})
        ;
    }
}
