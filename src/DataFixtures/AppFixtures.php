<?php

namespace App\DataFixtures;

use App\Entity\OrderItem;
use App\Factory\CustomerFactory;
use App\Factory\FoodFactory;
use App\Factory\OrderFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CustomerFactory::createMany(200);
        FoodFactory::createOne([
            'name' => 'Burger SM',
            'price' => 199.99
        ]);
        FoodFactory::createOne([
            'name' => 'Burger L',
            'price' => 299.99
        ]);
        FoodFactory::createOne([
            'name' => 'Burger XL',
            'price' => 359.99
        ]);
        FoodFactory::createOne([
            'name' => 'Fries',
            'price' => 99.99
        ]);
        FoodFactory::createOne([
            'name' => 'Burger Cheddar',
            'price' => 199.99
        ]);
        FoodFactory::createOne([
            'name' => 'Burger SM + Fries',
            'price' => 259.99
        ]);
        FoodFactory::createOne([
            'name' => 'Burger L + Fries',
            'price' => 359.99
        ]);
        FoodFactory::createOne([
            'name' => 'Soda 200ml',
            'price' => 99.99
        ]);
        FoodFactory::createOne([
            'name' => 'Soda 600ml',
            'price' => 199.99
        ]);
    }
}
