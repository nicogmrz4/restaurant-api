<?php

namespace App\Factory;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Food>
 *
 * @method        Food|Proxy create(array|callable $attributes = [])
 * @method static Food|Proxy createOne(array $attributes = [])
 * @method static Food|Proxy find(object|array|mixed $criteria)
 * @method static Food|Proxy findOrCreate(array $attributes)
 * @method static Food|Proxy first(string $sortedField = 'id')
 * @method static Food|Proxy last(string $sortedField = 'id')
 * @method static Food|Proxy random(array $attributes = [])
 * @method static Food|Proxy randomOrCreate(array $attributes = [])
 * @method static FoodRepository|RepositoryProxy repository()
 * @method static Food[]|Proxy[] all()
 * @method static Food[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Food[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Food[]|Proxy[] findBy(array $attributes)
 * @method static Food[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Food[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class FoodFactory extends ModelFactory
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
            'currentPrice' => self::faker()->randomFloat(2, 1000, 4000),
            'name' => self::faker()->sentence(3),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Food $food): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Food::class;
    }
}
