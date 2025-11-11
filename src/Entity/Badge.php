<?php

namespace App\Entity;

use App\Repository\BadgeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['badge:read']]
)]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['badge:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['badge:read'])]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['badge:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['badge:read'])]
    private ?string $icon = null;

    #[ORM\Column(length: 50)]
    #[Groups(['badge:read'])]
    private ?string $tier = 'bronze';

    #[ORM\Column]
    #[Groups(['badge:read'])]
    private int $requiredXp = 0;

    #[ORM\Column(nullable: true)]
    #[Groups(['badge:read'])]
    private ?int $requiredMastery = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['badge:read'])]
    private ?string $category = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function getTier(): ?string
    {
        return $this->tier;
    }

    public function setTier(string $tier): static
    {
        $this->tier = $tier;
        return $this;
    }

    public function getRequiredXp(): int
    {
        return $this->requiredXp;
    }

    public function setRequiredXp(int $requiredXp): static
    {
        $this->requiredXp = $requiredXp;
        return $this;
    }

    public function getRequiredMastery(): ?int
    {
        return $this->requiredMastery;
    }

    public function setRequiredMastery(?int $requiredMastery): static
    {
        $this->requiredMastery = $requiredMastery;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;
        return $this;
    }
}
