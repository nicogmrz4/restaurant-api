<?php

namespace App\Entity;

use App\Repository\FoodPriceHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FoodPriceHistoryRepository::class)]
class FoodPriceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['food:read'])]
    private ?float $price = null;
    
    #[ORM\Column]
    #[Groups(['food:read'])]
    private ?\DateTimeImmutable $periodFrom;
    
    #[ORM\Column(nullable: true)]
    #[Groups(['food:read'])]
    private ?\DateTimeImmutable $periodTo = null;

    #[ORM\ManyToOne(inversedBy: 'priceHistory')]
    private ?Food $food = null;

    #[ORM\Column]
    private ?bool $isCurrent = null;

    public function __construct()
    {
        $this->periodFrom = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPeriodFrom(): ?\DateTimeImmutable
    {
        return $this->periodFrom;
    }

    public function getPeriodTo(): ?\DateTimeImmutable
    {
        return $this->periodTo;
    }

    public function setPeriodTo(?\DateTimeImmutable $periodTo): static
    {
        $this->periodTo = $periodTo;

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

    public function setIsCurrent(bool $isCurrent): static
    {
        $this->isCurrent = $isCurrent;

        return $this;
    }
}
