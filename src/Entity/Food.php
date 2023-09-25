<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\FoodRepository;
use App\State\PostFoodProcessor;
use App\State\UpdateFoodProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
#[ApiResource(operations: [
    new Post(
        uriTemplate: '/foods',
        processor: PostFoodProcessor::class,
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
        processor: UpdateFoodProcessor::class,
    ),
])]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['food:read', 'food:write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['food:read', 'food:write'])]
    private ?float $currentPrice = null;

    #[ORM\OneToMany(mappedBy: 'food', targetEntity: FoodPriceHistory::class)]
    #[Groups(['food:read'])]
    private Collection $priceHistory;

    public function __construct()
    {
        $this->priceHistory = new ArrayCollection();
    }

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

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(float $currentPrice): static
    {
        $this->currentPrice = $currentPrice;

        return $this;
    }

    /**
     * @return Collection<int, FoodPriceHistory>
     */
    public function getPriceHistory(): Collection
    {
        return $this->priceHistory;
    }

    public function addPriceHistory(FoodPriceHistory $priceHistory): static
    {
        if (!$this->priceHistory->contains($priceHistory)) {
            $this->priceHistory->add($priceHistory);
            $priceHistory->setFood($this);
        }

        return $this;
    }

    public function removePriceHistory(FoodPriceHistory $priceHistory): static
    {
        if ($this->priceHistory->removeElement($priceHistory)) {
            // set the owning side to null (unless already changed)
            if ($priceHistory->getFood() === $this) {
                $priceHistory->setFood(null);
            }
        }

        return $this;
    }
}
