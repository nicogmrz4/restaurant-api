<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\State\OrderItemProcessor;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ApiResource(
    operations: [
        new Delete(),
        new Post(
            denormalizationContext: ['groups' => 'order-item:post'],
            processor: OrderItemProcessor::class
        )
    ],
)]
#[ApiResource(
    uriTemplate: '/orders/{orderId}/items',
    uriVariables: [
        'orderId' => new Link(fromClass: Order::class, toProperty: 'order'),
    ],
    operations: [ new GetCollection(
        normalizationContext: ['groups' => "order-item:get-collection"]
    ) ]
)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groups' => "order-item:get-collection"])]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'items')]
    #[Groups(['order-item:post'])]
    private ?Order $order = null;
    
    #[ORM\Column]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('integer')]
    #[Groups(['order:post', 'order-items:patch', 'order-item:post', 'order-item:get-collection'])]
    private ?int $quantity = null;
    
    #[ORM\Column]
    #[Groups(['groups' => "order-item:get-collection"])]
    private ?float $pricePerUnit = null;
    
    #[ORM\ManyToOne]
    #[Groups(['order:post', 'order-items:patch', 'order-item:post', 'order-item:get-collection'])]
    private ?Food $food = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getOrder(): ?Order
    {
        return $this->order;
    }
    
    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPricePerUnit(): ?float
    {
        return $this->pricePerUnit;
    }

    public function setPricePerUnit(float $pricePerUnit): static
    {
        $this->pricePerUnit = $pricePerUnit;

        return $this;
    }

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(?Food $food): static
    {
        $this->food = $food;

        return $this;
    }
}
