<?php

namespace App\Story;

use App\Entity\OrderItem;
use App\Factory\CustomerFactory;
use App\Factory\FoodFactory;
use App\Factory\OrderFactory;
use App\Factory\OrderItemFactory;
use Zenstruck\Foundry\Story;

final class OrdersStory extends Story
{
    public function build(): void
    {
        OrderFactory::createMany(100, function() {
            $items = OrderItemFactory::createMany(rand(1, 5), function() {
                $food = FoodFactory::random();
                return [
                    "food" => $food,
                    "pricePerUnit" => $food->getPrice(),
		    "quantity" => rand(1, 5)
                ];
            });
            
            $totalItems = 0;
            $totalPrice = 0;
    
            foreach ($items as $key => $item) {
                $totalItems += $item->getQuantity();
                $totalPrice += $item->getPricePerUnit() * $item->getQuantity();
            }

            return [
                "customer" => CustomerFactory::random(),
                "items" => $items,
                "totalItems" => $totalItems,
                "totalPrice" => $totalPrice
            ];
        });
    }
}
