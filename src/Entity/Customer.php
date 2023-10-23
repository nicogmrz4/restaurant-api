<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CustomerRepository;
use App\State\CustomerProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 50,
    processor: CustomerProcessor::class,
    normalizationContext: ['groups' => 'customer:read'], 
    denormalizationContext: ['groups' => 'customer:write'] 
)]
#[ApiFilter(SearchFilter::class, properties: ['dni' => 'exact'])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['customer:read'])]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $firstName = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $lastName = null;
    
    #[ORM\Column]
    #[Assert\GreaterThan(0)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?int $dni = null;
    
    #[ORM\Column]
    #[Assert\GreaterThan(0)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?int $phoneNumber = null;
    
    #[ORM\Column]
    #[Groups(['customer:read'])]
    private ?\DateTimeImmutable $createdAt = null;
    
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class,  cascade: ['persist', 'remove'])]
    private Collection $orders;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDni(): ?int
    {
        return $this->dni;
    }

    public function setDni(int $dni): static
    {
        $this->dni = $dni;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    // public function addOrder(Order $order): static
    // {
    //     if (!$this->orders->contains($order)) {
    //         $this->orders->add($order);
    //         $order->setCustomer($this);
    //     }

    //     return $this;
    // }

    // public function removeOrder(Order $order): static
    // {
    //     if ($this->orders->removeElement($order)) {
    //         // set the owning side to null (unless already changed)
    //         if ($order->getCustomer() === $this) {
    //             $order->setCustomer(null);
    //         }
    //     }

    //     return $this;
    // }
}
