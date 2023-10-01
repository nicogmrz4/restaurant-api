<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\CustomerFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AnalyticsRecordTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function testRecordTotalOrders(): void
    {
        for ($i=0; $i < 20; $i++) { 
            $this->createOrder();
        }

        $response = static::createClient()->request('GET', '/api/analytics_recorders/total_orders');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'value' => 20,
            'record' => 'total_orders'
        ]);
    }

    public function createOrder() {
        $customer = CustomerFactory::createOne();

        static::createClient()->request(
            'POST',
            '/api/orders',
            [
                'json' => [
                    'customer' => '/api/customers/' . $customer->getId(),
                    'items' => [
                        ['name' => 'Burger XL', 'quantity' => 2, 'pricePerUnit' => 250.2],
                        ['name' => 'Burger XXL', 'quantity' => 1, 'pricePerUnit' => 500.2],
                    ]
                ]
            ]
        );
    }
}
