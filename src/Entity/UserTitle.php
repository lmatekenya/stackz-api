<?php

namespace App\Entity;

use App\Repository\UserTitleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserTitleRepository::class)]
class UserTitle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private bool $isDisplayed = false;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private bool $isActive = false;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeInterface $unlockedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function isDisplayed(): bool
    {
        return $this->isDisplayed;
    }

    public function setIsDisplayed(bool $isDisplayed): static
    {
        $this->isDisplayed = $isDisplayed;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getUnlockedAt(): ?\DateTimeInterface
    {
        return $this->unlockedAt;
    }

    public function setUnlockedAt(\DateTimeInterface $unlockedAt): static
    {
        $this->unlockedAt = $unlockedAt;
        return $this;
    }
}
