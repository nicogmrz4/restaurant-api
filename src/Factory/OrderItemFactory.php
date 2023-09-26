<?php

namespace App\Factory;

use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<OrderItem>
 *
 * @method        OrderItem|Proxy create(array|callable $attributes = [])
 * @method static OrderItem|Proxy createOne(array $attributes = [])
 * @method static OrderItem|Proxy find(object|array|mixed $criteria)
 * @method static OrderItem|Proxy findOrCreate(array $attributes)
 * @method static OrderItem|Proxy first(string $sortedField = 'id')
 * @method static OrderItem|Proxy last(string $sortedField = 'id')
 * @method static OrderItem|Proxy random(array $attributes = [])
 * @method static OrderItem|Proxy randomOrCreate(array $attributes = [])
 * @method static OrderItemRepository|RepositoryProxy repository()
 * @method static OrderItem[]|Proxy[] all()
 * @method static OrderItem[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static OrderItem[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static OrderItem[]|Proxy[] findBy(array $attributes)
 * @method static OrderItem[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static OrderItem[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class OrderItemFactory extends ModelFactory
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
            'name' => self::faker()->sentence(2),
            'pricePerUnit' => self::faker()->randomFloat(2, 100, 1000),
            'quantity' => self::faker()->randomNumber(1),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(OrderItem $orderItem): void {})
        ;
    }

    protected static function getClass(): string
    {
        return OrderItem::class;
    }
}
