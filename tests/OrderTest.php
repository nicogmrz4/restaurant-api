<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\ClientFactory;
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
        $client = ClientFactory::createOne();

        $response = static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'client' => '/api/clients/' . $client->getId(),
                    'items' => [
                        ['name' => 'Burger XL', 'quantity' => 2, 'pricePerUnit' => 250.2],
                        ['name' => 'Burger XXL', 'quantity' => 1, 'pricePerUnit' => 500.2],
                    ]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'totalItems' => 3,
            'totalPrice' => 1000.6,
            'status' => 'pending'
        ]);
        $responseAsArray = $response->toArray();
        $this->assertMatchesRegularExpression('~^/api/orders/\d+$~', $responseAsArray['@id']);
        $this->assertCount(2, $responseAsArray['items']);
    }

    public function testCreateWihtoutItems(): void
    {
        $client = ClientFactory::createOne();

        static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'client' => '/api/clients/' . $client->getId(),
                    'items' => []
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testDelete(): void
    {
        $client = ClientFactory::createOne();

        $response = static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'client' => '/api/clients/' . $client->getId(),
                    'items' => [
                        ['name' => 'Burger XL', 'quantity' => 2, 'pricePerUnit' => 250.2],
                        ['name' => 'Burger XXL', 'quantity' => 1, 'pricePerUnit' => 500.2],
                    ]
                ]
            ]
        );

        $response = static::createClient()
            ->request('DELETE', $response->toArray()['@id']);
        $this->assertResponseStatusCodeSame(204);
    }

    public function testUpdate(): void
    {
        $client = ClientFactory::createOne();
        $items = OrderItemFactory::createMany(4);
        $order = OrderFactory::createOne([
            'client' => $client,
            'items' => $items
        ]); 

        $orderUri = '/api/orders/'.$order->getId().'/items';
        
        $response = static::createClient()->request(
            'PATCH',
            $orderUri,
            [
                'json' => [
                    'items' => [
                        ['name' => 'Burger SM', 'quantity' => 5, 'pricePerUnit' => 500],
                        ['name' => 'Burger XXL', 'quantity' => 2, 'pricePerUnit' => 1000.25],
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ]
        );
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'totalItems' => 7,
            'totalPrice' => 4500.5
        ]);
        $responseItems = $response->toArray()['items'];
        $this->assertCount(2, $responseItems);
        
        $expectedItems = [
            ['name' => 'Burger SM', 'quantity' => 5, 'pricePerUnit' => 500],
            ['name' => 'Burger XXL', 'quantity' => 2, 'pricePerUnit' => 1000.25],
        ];

        $assertsCount = 0;

        foreach ($responseItems as $resKey => $responseItem) {
            foreach ($expectedItems as $expeKey => $expectedItem) {
                foreach ($expectedItem as $key => $value) {
                    if ($responseItem[$key] == $value) $assertsCount++;
                    else break;
                }
            }
        }
        
        $this->assertTrue($assertsCount === 6, 'Some properties hasn\'t been updated');
    }

    public function testUpdateWihtoutItems(): void
    {
        $client = ClientFactory::createOne();
        $items = OrderItemFactory::createMany(4);
        $order = OrderFactory::createOne([
            'client' => $client,
            'items' => $items
        ]); 

        $orderUri = '/api/orders/'.$order->getId().'/items';
        
        $response = static::createClient()->request(
            'PATCH',
            $orderUri,
            [
                'json' => [
                    'items' => []
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(1, $response->toArray(false)['violations']);
    }

    public function testCreateBlankItemName(): void
    {
        $client = ClientFactory::createOne();

        $response = static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'client' => '/api/clients/' . $client->getId(),
                    'items' => [
                        ['name' => '', 'quantity' => 2, 'pricePerUnit' => 250.2],
                    ]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(1, $response->toArray(false)['violations']);
    }

    public function testCreateInvalidItemQuantity(): void
    {
        $client = ClientFactory::createOne();

        $response = static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'client' => '/api/clients/' . $client->getId(),
                    'items' => [
                        ['name' => 'Burger XXL', 'quantity' => 0, 'pricePerUnit' => 250.2],
                    ]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(1, $response->toArray(false)['violations']);
    }

    public function testCreateInvalidItemPricePerUnit(): void
    {
        $client = ClientFactory::createOne();

        $response = static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'client' => '/api/clients/' . $client->getId(),
                    'items' => [
                        ['name' => 'Burger XXL', 'quantity' => 1, 'pricePerUnit' => 0],
                    ]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(1, $response->toArray(false)['violations']);
    }

    public function testUpdateBlankItemName(): void
    {
        $client = ClientFactory::createOne();
        $items = OrderItemFactory::createMany(4);
        $order = OrderFactory::createOne([
            'client' => $client,
            'items' => $items
        ]); 

        $orderUri = '/api/orders/'.$order->getId().'/items';
        
        $response = static::createClient()->request(
            'PATCH',
            $orderUri,
            [
                'json' => [
                    'items' => [
                        ['name' => 'Burger SM', 'quantity' => 5, 'pricePerUnit' => 500],
                        ['name' => '', 'quantity' => 2, 'pricePerUnit' => 1000.25],
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(1, $response->toArray(false)['violations']);
    }

    public function testUpdateInvalidItemQuanityValue(): void
    {
        $client = ClientFactory::createOne();
        $items = OrderItemFactory::createMany(4);
        $order = OrderFactory::createOne([
            'client' => $client,
            'items' => $items
        ]); 

        $orderUri = '/api/orders/'.$order->getId().'/items';
        
        $response = static::createClient()->request(
            'PATCH',
            $orderUri,
            [
                'json' => [
                    'items' => [
                        ['name' => 'Burger SM', 'quantity' => 5, 'pricePerUnit' => 500],
                        ['name' => 'Burger XXL', 'quantity' => 0, 'pricePerUnit' => 1000.25],
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(1, $response->toArray(false)['violations']);
    }

    public function testUpdateInvalidItemPricePerUnit(): void
    {
        $client = ClientFactory::createOne();
        $items = OrderItemFactory::createMany(4);
        $order = OrderFactory::createOne([
            'client' => $client,
            'items' => $items
        ]); 

        $orderUri = '/api/orders/'.$order->getId().'/items';
        
        $response = static::createClient()->request(
            'PATCH',
            $orderUri,
            [
                'json' => [
                    'items' => [
                        ['name' => 'Burger SM', 'quantity' => 5, 'pricePerUnit' => 500],
                        ['name' => 'Burger XXL', 'quantity' => 1, 'pricePerUnit' => -1000.25],
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(422);
        $this->assertCount(1, $response->toArray(false)['violations']);
    }
}
