<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\CustomerFactory;
use App\Factory\FoodFactory;
use App\Factory\OrderFactory;
use App\Factory\OrderItemFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function testGetCollection(): void
    {
        OrderFactory::createMany(100);

        $response = static::createClient()->request('GET', '/api/orders');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Order',
            '@id' => '/api/orders',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/orders?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/orders?page=1',
                'hydra:last' => '/api/orders?page=4',
                'hydra:next' => '/api/orders?page=2'
            ]
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertArrayNotHasKey('items', $response->toArray()['hydra:member'][0]);
        $this->assertArrayHasKey('status', $response->toArray()['hydra:member'][0]);
    }

    public function testCreate(): void
    {
        $customer = CustomerFactory::createOne();
        $food1 = FoodFactory::randomOrCreate();
        $food2 = FoodFactory::randomOrCreate();

        $response = static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'customer' => '/api/customers/' . $customer->getId(),
                    'items' => [
                        ['food' => '/api/foods/'.$food1->getId(), 'quantity' => 2],
                        ['food' => '/api/foods/'.$food2->getId(), 'quantity' => 1],
                    ]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'totalItems' => 3,
            'totalPrice' => round(($food1->getPrice() * 2) + $food2->getPrice(), 2),
            'status' => 'pending'
        ]);
        $responseAsArray = $response->toArray();
        $this->assertMatchesRegularExpression('~^/api/orders/\d+$~', $responseAsArray['@id']);
        $this->assertCount(2, $responseAsArray['items']);
    }

    public function testCreateWihtoutItems(): void
    {
        $customer = CustomerFactory::createOne();

        static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'customer' => '/api/customers/' . $customer->getId(),
                    'items' => []
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testDelete(): void
    {
        $customer = CustomerFactory::createOne();
        $food1 = FoodFactory::randomOrCreate();
        $food2 = FoodFactory::randomOrCreate();

        $response = static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'customer' => '/api/customers/' . $customer->getId(),
                    'items' => [
                        ['food' => '/api/foods/'.$food1->getId(), 'quantity' => 2],
                        ['food' => '/api/foods/'.$food2->getId(), 'quantity' => 1],
                    ]
                ]
            ]
        );

        $response = static::createClient()
            ->request('DELETE', $response->toArray()['@id']);
        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteNotFound(): void
    {
        $response = static::createClient()
            ->request('DELETE', '/api/orders/2231233');
        $this->assertResponseStatusCodeSame(404);
    }


    public function testUpdateOrderStatus() {
        $customer = CustomerFactory::createOne();
        $items = OrderItemFactory::createMany(4);
        $order = OrderFactory::createOne([
            'customer' => $customer,
            'items' => $items,
            'status' => 'adsaad'
        ]);

        $orderUri = '/api/orders/'.$order->getId();

        $response = static::createClient()->request(
            'PUT',
            $orderUri.'/status',
            [
                'json' => [
                    'status' => 'pending' 
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'status' => 'pending'
        ]);
    }

    public function testUpdateOrderStatusWithInvalidStatus() {
        $customer = CustomerFactory::createOne();
        $items = OrderItemFactory::createMany(4);
        $order = OrderFactory::createOne([
            'customer' => $customer,
            'items' => $items,
            'status' => 'adsaad'
        ]);

        $orderUri = '/api/orders/'.$order->getId();

        $response = static::createClient()->request(
            'PUT',
            $orderUri.'/status',
            [
                'json' => [
                    'status' => 'asdfasda'
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }
}
