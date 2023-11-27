<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\CustomerFactory;
use App\Service\AnalyticsRecorderService;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AnalyticsRecordTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function testRecordTotalOrders(): void
    {
        $container = $this->getContainer();
        $analyticsRecordsSvc = $container->get(AnalyticsRecorderService::class);

        for ($i=0; $i < 20; $i++) { 
            $analyticsRecordsSvc->recordTotalOrders();
        }

        $this->createClient()->request('GET', '/api/analytics_recorders/total_orders');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'value' => 20,
            'record' => 'total_orders'
        ]);
    }

    public function testRecordTotalCustomers(): void
    {
        $container = $this->getContainer();
        $analyticsRecordsSvc = $container->get(AnalyticsRecorderService::class);

        for ($i=0; $i < 20; $i++) { 
            $analyticsRecordsSvc->recordTotalCustomers();
        }
        
        static::createClient()->request('GET', '/api/analytics_recorders/total_customers');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'value' => 20,
            'record' => 'total_customers'
        ]);
    }
}
