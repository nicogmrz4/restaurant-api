<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Customer;
use App\Factory\CustomerFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CustomerTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function getDefaultClientAsArray(array $atributes = [])
    {
        $customerAsArray = [
            'firstName' => 'John',
            'lastName' => 'McGregor',
            'phoneNumber' => '123456789',
            'dni' => '123456789'
        ];

        foreach ($atributes as $attr => $value) {
            if (isset($customerAsArray[$attr])) {
                $customerAsArray[$attr] = $value;
            }
        }

        return $customerAsArray;
    }

    public function testGetCollection(): void
    {
        CustomerFactory::createMany(100);

        $response = static::createClient()->request('GET', '/api/customers');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Customer',
            '@id' => '/api/customers',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/customers?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/customers?page=1',
                'hydra:last' => '/api/customers?page=2',
                'hydra:next' => '/api/customers?page=2'
            ]
        ]);

        $this->assertCount(50, $response->toArray()['hydra:member']);
    }

    public function testCreate(): void
    {
        $json = $this->getDefaultClientAsArray();

        $response = static::createClient()->request('POST', '/api/customers', ['json' => $json]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Customer',
            '@type' => 'Customer',
            'firstName' => 'John',
            'lastName' => 'McGregor',
            'phoneNumber' => '123456789',
            'dni' => '123456789'
        ]);
        $this->assertMatchesRegularExpression('~^/api/customers/\d+$~', $response->toArray()['@id']);
    }

    public function testUpdateDni(): void
    {
        $customer = CustomerFactory::createOne();
        $customerIri = $this->findIriBy(Customer::class, ['id' => $customer->getId()]);

        $httpClient = static::createClient();

        $httpClient->request('PATCH', $customerIri, [
            'json' => [
                'dni' => '987654321'
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $customerIri,
            'dni' => '987654321'
        ]);
    }

    public function testUpdateFirstName(): void
    {
        $customer = CustomerFactory::createOne();
        $customerIri = $this->findIriBy(Customer::class, ['id' => $customer->getId()]);

        $httpClient = static::createClient();
        $httpClient->request('PATCH', $customerIri, [
            'json' => [
                'firstName' => 'Jorge'
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $customerIri,
            'firstName' => 'Jorge'
        ]);
    }

    public function testUpdateLastName(): void
    {
        $customer = CustomerFactory::createOne();
        $customerIri = $this->findIriBy(Customer::class, ['id' => $customer->getId()]);

        $httpClient = static::createClient();

        $httpClient->request('PATCH', $customerIri, [
            'json' => [
                'lastName' => 'Guitierrez'
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $customerIri,
            'lastName' => 'Guitierrez'
        ]);
    }

    public function testUpdatePhoneNumber(): void
    {
        $customer = CustomerFactory::createOne();
        $customerIri = $this->findIriBy(Customer::class, ['id' => $customer->getId()]);

        $httpClient = static::createClient();

        $httpClient->request('PATCH', $customerIri, [
            'json' => [
                'phoneNumber' => '99991111'
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $customerIri,
            'phoneNumber' => '99991111'
        ]);
    }

    public function testCreateClientWithBlankFirstName()
    {
        $json = $this->getDefaultClientAsArray(['firstName' => '']);
        static::createClient()->request('POST', '/api/customers', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateWithBlankLastName()
    {
        $json = $this->getDefaultClientAsArray(['lastName' => '']);
        static::createClient()->request('POST', '/api/customers', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateWithInvalidPhoneNumber()
    {
        $json = $this->getDefaultClientAsArray(['phoneNumber' => -111]);
        static::createClient()->request('POST', '/api/customers', ['json' => $json]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateWithInvalidDni()
    {
        $json = $this->getDefaultClientAsArray(['dni' => -111]);
        static::createClient()->request('POST', '/api/customers', ['json' => $json]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateWithBlankNumber()
    {
        $json = $this->getDefaultClientAsArray(['phoneNumber' => '']);
        static::createClient()->request('POST', '/api/customers', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateWithBlankDni()
    {
        $json = $this->getDefaultClientAsArray(['dni' => '']);
        static::createClient()->request('POST', '/api/customers', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }
}
