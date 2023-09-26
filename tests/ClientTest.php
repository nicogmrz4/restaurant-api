<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Client;
use App\Factory\ClientFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ClientTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    public function getDefaultClientAsArray(array $atributes = []) {
        $clientAsArray = [
            'firstName' => 'John',
            'lastName' => 'McGregor',
            'phoneNumber' => 123456789,
            'dni' => 123456789
        ];

        foreach ($atributes as $attr => $value) {
            if (isset($clientAsArray[$attr])) {
                $clientAsArray[$attr] = $value;
            }
        }

        return $clientAsArray;
    }

    public function testGetCollection(): void
    {
        ClientFactory::createMany(100);

        $response = static::createClient()->request('GET', '/api/clients');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Client',
            '@id' => '/api/clients',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/clients?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/clients?page=1',
                'hydra:last' => '/api/clients?page=4',
                'hydra:next' => '/api/clients?page=2'
            ]
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
    }

    public function testCreate(): void
    {
        $json = $this->getDefaultClientAsArray();

        $response = static::createClient()->request('POST', '/api/clients', ['json' => $json]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Client',
            '@type' => 'Client',
            'firstName' => 'John',
            'lastName' => 'McGregor',
            'phoneNumber' => 123456789,
            'dni' => 123456789
        ]);
        $this->assertMatchesRegularExpression('~^/api/clients/\d+$~', $response->toArray()['@id']);
    }

    public function testUpdateDni(): void
    {
        ClientFactory::createOne([
            'dni' => 123456789
        ]);

        $clientIri = $this->findIriBy(Client::class, ['dni' => '123456789']);

        $httpClient = static::createClient();

        $httpClient->request('PATCH', $clientIri, [
            'json' => [
                'dni' => 987654321
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $clientIri,
            'dni' => 987654321
        ]);
    }

    public function testUpdateFirstName(): void
    {
        ClientFactory::createOne([
            'firstName' => 'Marcos'
        ]);

        $clientIri = $this->findIriBy(Client::class, ['firstName' => 'Marcos']);

        $httpClient = static::createClient();
        $httpClient->request('PATCH', $clientIri, [
            'json' => [
                'firstName' => 'Jorge'
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $clientIri,
            'firstName' => 'Jorge'
        ]);
    }

    public function testUpdateLastName(): void
    {
        ClientFactory::createOne([
            'lastName' => 'Garcia'
        ]);

        $clientIri = $this->findIriBy(Client::class, ['lastName' => 'Garcia']);

        $httpClient = static::createClient();
        
        $httpClient->request('PATCH', $clientIri, [
            'json' => [
                'lastName' => 'Guitierrez'
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $clientIri,
            'lastName' => 'Guitierrez'
        ]);
    }

    public function testUpdatePhoneNumber(): void
    {
        ClientFactory::createOne([
            'phoneNumber' => 44446666
        ]);

        $clientIri = $this->findIriBy(Client::class, ['phoneNumber' => 44446666]);

        $httpClient = static::createClient();
        
        $httpClient->request('PATCH', $clientIri,[
            'json' => [
                'phoneNumber' => 99991111
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $clientIri,
            'phoneNumber' => 99991111
        ]);
    }

    public function testCreateClientWithBlankFirstName() {
        $json = $this->getDefaultClientAsArray(['firstName' => '']);
        static::createClient()->request('POST', '/api/clients', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateWithBlankLastName() {
        $json = $this->getDefaultClientAsArray(['lastName' => '']);
        static::createClient()->request('POST', '/api/clients', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateWithInvalidPhoneNumber() {
        $json = $this->getDefaultClientAsArray(['phoneNumber' => -111]);
        static::createClient()->request('POST', '/api/clients', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateWithInvalidDni() {
        $json = $this->getDefaultClientAsArray(['dni' => -111]);
        static::createClient()->request('POST', '/api/clients', ['json' => $json]);

        $this->assertResponseStatusCodeSame(422);
    }
}
