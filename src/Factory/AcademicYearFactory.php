<?php

namespace App\Factory;

use App\Entity\AcademicYear;
use App\Repository\AcademicYearRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<AcademicYear>
 *
 * @method        AcademicYear|Proxy                     create(array|callable $attributes = [])
 * @method static AcademicYear|Proxy                     createOne(array $attributes = [])
 * @method static AcademicYear|Proxy                     find(object|array|mixed $criteria)
 * @method static AcademicYear|Proxy                     findOrCreate(array $attributes)
 * @method static AcademicYear|Proxy                     first(string $sortedField = 'id')
 * @method static AcademicYear|Proxy                     last(string $sortedField = 'id')
 * @method static AcademicYear|Proxy                     random(array $attributes = [])
 * @method static AcademicYear|Proxy                     randomOrCreate(array $attributes = [])
 * @method static AcademicYearRepository|RepositoryProxy repository()
 * @method static AcademicYear[]|Proxy[]                 all()
 * @method static AcademicYear[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static AcademicYear[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static AcademicYear[]|Proxy[]                 findBy(array $attributes)
 * @method static AcademicYear[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static AcademicYear[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class AcademicYearFactory extends ModelFactory
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
            'endDate' => self::faker()->dateTime(),
            'startDate' => self::faker()->dateTime(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(AcademicYear $academicYear): void {})
        ;
    }

    protected static function getClass(): string
    {
        return AcademicYear::class;
    }
}
