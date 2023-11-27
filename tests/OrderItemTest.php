<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\FoodFactory;
use App\Factory\OrderFactory;
use App\Factory\OrderItemFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderItemTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    const END_POINT = "/api/order_items";

    public function testGetOrderItems() 
    {
        $order = OrderFactory::createOne();

        OrderItemFactory::createMany(10, [
            "order" => $order
        ]);

        $response = $this->createClient()->request("GET", sprintf("/api/orders/%d/items", $order->getId()));

        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $response->toArray()['hydra:member']);
    }

    public function testCreateOrderItem()
    {
        $food = FoodFactory::randomOrCreate();
        $order = OrderFactory::randomOrCreate();

        $orderItem = [
            "quantity" => 1,
            "food" => sprintf("/api/foods/%d", $food->getId()),
            "order" => sprintf("/api/orders/%d", $order->getId()),
        ];

        $response = $this->createClient()
            ->request("POST", self::END_POINT, [
                "json" => $orderItem
            ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateOrderItemNonexistentFood()
    {
        $food = FoodFactory::randomOrCreate();
        $order = OrderFactory::randomOrCreate();

        $orderItem = [
            "quantity" => 1,
            "food" => sprintf("/api/foods/%d", 5000),
            "order" => sprintf("/api/orders/%d", $order->getId()),
        ];


        $response = $this->createClient()
            ->request("POST", self::END_POINT, [
                "json" => $orderItem
            ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateOrderItemNonexistenOrder()
    {
        $food = FoodFactory::randomOrCreate();

        $orderItem = [
            "quantity" => 1,
            "food" => sprintf("/api/foods/%d", $food->getId()),
            "order" => sprintf("/api/orders/%d", 5000),
        ];

        $response = $this->createClient()
            ->request("POST", self::END_POINT, [
                "json" => $orderItem
            ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testDeleteOrderItem()
    {
        $orderItem = OrderItemFactory::randomOrCreate();

        $this->createClient()->request("DELETE", sprintf(self::END_POINT . "/%s", $orderItem->getId()));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteNonexistentOrderItem()
    {
        $this->createClient()->request("DELETE", sprintf(self::END_POINT . "/%s", 5000));

        $this->assertResponseStatusCodeSame(404);
    }
}
