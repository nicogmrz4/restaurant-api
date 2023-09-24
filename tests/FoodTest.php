<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Food;
use App\Factory\FoodFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class FoodTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function getDefaultFoodAsArray(array $atributes = []) {
        $foodAsArray = [
            'name' => 'Pizza Grande De Muzzarella',
            'currentPrice' => 2499.99
        ];

        foreach ($atributes as $attr => $value) {
            if (isset($clientAsArray[$attr])) {
                $clientAsArray[$attr] = $value;
            }
        }

        return $foodAsArray;
    }

    public function testGetCollection(): void
    {
        FoodFactory::createMany(100);

        $response = static::createClient()->request('GET', '/api/foods');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Food',
            '@id' => '/api/foods',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/foods?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/foods?page=1',
                'hydra:last' => '/api/foods?page=4',
                'hydra:next' => '/api/foods?page=2'
            ]
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
    }

    public function testCreate(): void
    {
        $json = $this->getDefaultFoodAsArray();
        $response = static::createClient()->request('POST', '/api/foods', ['json' => $json]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesRegularExpression('~^/api/foods/\d+$~', $response->toArray()['@id']);
        $this->assertCount(1, $response->toArray()['priceHistory']);
    }

    public function testUpdateName(): void
    {
        FoodFactory::createOne(['name' => 'Pizza XL', 'currentPirce' => 299.99]);

        $foodIri = $this->findIriBy(Food::class, ['name' => 'Pizza XL']);

        $httpClient = static::createClient();

        $httpClient->request('PUT', $foodIri, [
            'json' => [
                'name' => 'Empanada de carne'
            ],
            'headers' => [
                'Content-Type: application/ld+json',
                'accept: application/ld+json'
            ]
            ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $foodIri,
            'name' => 'Empanada de carne',
            'currentPrice' => 299.99
        ]);
    }

    public function testUpdateCurrentPrice(): void
    {
        $json = $this->getDefaultFoodAsArray();
        $createResponse = static::createClient()->request('POST', '/api/foods', ['json' => $json]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesRegularExpression('~^/api/foods/\d+$~', $createResponse->toArray()['@id']);
        $this->assertCount(1, $createResponse->toArray()['priceHistory']);

        $foodIri = $createResponse->toArray()['@id'];

        $httpClient = static::createClient();

        $response = $httpClient->request('PUT', $foodIri, [
            'json' => [
                'currentPrice' => 1999.99
            ],
            'headers' => [
                'Content-Type: application/ld+json',
                'accept: application/ld+json'
            ]
        ]);
        
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $foodIri,
            'name' => $json['name'],
            'currentPrice' => 1999.99
        ]);
        $this->assertCount(2, $response->toArray()['priceHistory']);
    }
}
