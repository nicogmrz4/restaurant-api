<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
#[ApiResource(operations: [
    new Post(
        uriTemplate: '/foods',
        denormalizationContext: ['groups' => ['food:write']]
    ),
    new GetCollection(
        uriTemplate: '/foods',
        normalizationContext: ['groups' => ['food:read']]
    ),
    new Get(
        uriTemplate: '/foods/{id}',
        requirements: ['id' => '\d+']
    ),
    new Put(
        uriTemplate: '/foods/{id}',
        requirements: ['id' => '\d+'],
    ),
])]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['food:read', "order-item:get-collection"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['food:read', 'food:write', "order-item:get-collection"])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['food:read', 'food:write', "order-item:get-collection"])]
    private ?float $price = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
