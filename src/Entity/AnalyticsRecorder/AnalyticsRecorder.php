<?php

namespace App\Entity\AnalyticsRecorder;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AnalyticsRecorder\AnalyticsRecorderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;

#[ORM\Entity(repositoryClass: AnalyticsRecorderRepository::class)]
#[ApiResource(operations: [
    new Get(
        openapi: new Model\Operation(
            parameters: [
                new Model\Parameter(
                    name: 'record',
                    in: 'path',
                    description: 'Record name',
                    required: true
                )
            ]
        )
    )
])]
class AnalyticsRecorder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\OneToMany(mappedBy: 'analyticsRecorder', targetEntity: AnalyticsRecorderPerDay::class, cascade: ['persist', 'remove'])]
    private Collection $perDay;

    #[ORM\Column(length: 255)]
    #[ApiProperty(identifier: true)]
    private ?string $record = null;

    public function __construct()
    {
        $this->perDay = new ArrayCollection();
        $this->value = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, AnalyticsRecorderPerDay>
     */
    public function getPerDay(): Collection
    {
        return $this->perDay;
    }

    public function addPerDay(AnalyticsRecorderPerDay $perDay): static
    {
        if (!$this->perDay->contains($perDay)) {
            $this->perDay->add($perDay);
            $perDay->setAnalyticsRecorder($this);
        }

        return $this;
    }

    public function removePerDay(AnalyticsRecorderPerDay $perDay): static
    {
        if ($this->perDay->removeElement($perDay)) {
            // set the owning side to null (unless already changed)
            if ($perDay->getAnalyticsRecorder() === $this) {
                $perDay->setAnalyticsRecorder(null);
            }
        }

        return $this;
    }

    public function getRecord(): ?string
    {
        return $this->record;
    }

    public function setRecord(string $record): static
    {
        $this->record = $record;

        return $this;
    }
}
