<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Order $owner = null;
    
    #[ORM\Column(length: 255)]
    #[Groups(['order:post', 'order-items:patch'])]
    #[Assert\NotBlank]
    private ?string $name = null;
    
    #[ORM\Column]
    #[Groups(['order:post', 'order-items:patch'])]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('integer')]
    private ?int $quantity = null;
    
    #[ORM\Column]
    #[Groups(['order:post', 'order-items:patch'])]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('float')]
    private ?float $pricePerUnit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?Order
    {
        return $this->owner;
    }

    public function setOwner(?Order $owner): static
    {
        $this->owner = $owner;

        return $this;
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
}
