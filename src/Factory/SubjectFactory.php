<?php

namespace App\Factory;

use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Subject>
 *
 * @method        Subject|Proxy                     create(array|callable $attributes = [])
 * @method static Subject|Proxy                     createOne(array $attributes = [])
 * @method static Subject|Proxy                     find(object|array|mixed $criteria)
 * @method static Subject|Proxy                     findOrCreate(array $attributes)
 * @method static Subject|Proxy                     first(string $sortedField = 'id')
 * @method static Subject|Proxy                     last(string $sortedField = 'id')
 * @method static Subject|Proxy                     random(array $attributes = [])
 * @method static Subject|Proxy                     randomOrCreate(array $attributes = [])
 * @method static SubjectRepository|RepositoryProxy repository()
 * @method static Subject[]|Proxy[]                 all()
 * @method static Subject[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Subject[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Subject[]|Proxy[]                 findBy(array $attributes)
 * @method static Subject[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Subject[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class SubjectFactory extends ModelFactory
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
            'description' => self::faker()->text(255),
            'name' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Subject $subject): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Subject::class;
    }
}
