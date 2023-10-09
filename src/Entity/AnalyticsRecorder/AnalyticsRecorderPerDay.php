<?php

namespace App\Entity\AnalyticsRecorder;

use App\Repository\AnalyticsRecorder\AnalyticsRecorderPerDayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalyticsRecorderPerDayRepository::class)]
class AnalyticsRecorderPerDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'perDay')]
    private ?AnalyticsRecorder $analyticsRecorder = null;

    public function __construct()  
    {
        $today = (new \DateTimeImmutable())->setTime(0, 0);
        $this->createdAt = $today;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAnalyticsRecorder(): ?AnalyticsRecorder
    {
        return $this->analyticsRecorder;
    }

    public function setAnalyticsRecorder(?AnalyticsRecorder $analyticsRecorder): static
    {
        $this->analyticsRecorder = $analyticsRecorder;

        return $this;
    }
}
