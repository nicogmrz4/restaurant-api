<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model;
use App\Controller\OrderStatusController;
use App\Repository\OrderRepository;
use App\State\OrderProcessor;
use ArrayObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource(
    operations: [
        new Post(
            processor: OrderProcessor::class,
            denormalizationContext: ['groups' => 'order:post']
        ),
        new Put(
            uriTemplate: '/orders/{id}/status',
            controller: OrderStatusController::class,
            requirements: ['id' => '\d+'],
            write: false,
            validate: false,
            status: 200,
            openapi: new Model\Operation(
                summary: 'Update order status',
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'status' => ['type' => 'string'],
                                ]
                            ],
                        ]
                    ])
                ),
                // parameters: [
                //     new Model\Parameter(
                //         name: 'id',
                //         required: true,
                //         description: 'Order identifier',
                //         in: 'path'
                //     ),
                // ],
                responses: [
                    '200' => new Model\Response(
                        description: 'Order status updated',
                        content: new ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'status' => ['type' => 'string'],
                                    ]
                                ],
                            ]
                        ])
                    )
                ]
            )
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'order:get-collection']
        ),
        new Delete(),
        new Patch(
            uriTemplate: '/orders/{id}/items',
            requirements: ['id' => '\d+'],
            processor: OrderProcessor::class,
            denormalizationContext: ['groups' => 'order-items:patch']
        )
    ]
)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[Groups(['order:post', 'order:get-collection'])]
    private ?Customer $customer = null;

    #[ORM\Column]
    #[Groups(['order:get-collection'])]
    private ?float $totalPrice = null;

    #[ORM\Column]
    #[Groups(['order:get-collection'])]
    private ?int $totalItems = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    #[Groups(['order:post', 'order-items:patch'])]
    #[Assert\Count(min: 1)]
    private Collection $items;

    #[ORM\Column]
    #[Groups(['order:get-collection'])]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(length: 255)]
    #[Groups(['order:get-collection'])]
    private ?string $status = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->status = 'pending';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getTotalItems(): ?int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $totalItems): static
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOwner($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOwner() === $this) {
                $item->setOwner(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
