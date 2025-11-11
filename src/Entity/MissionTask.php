<?php

namespace App\Entity;

use App\Repository\MissionTaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;

#[ORM\Entity(repositoryClass: MissionTaskRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: 'is_granted("ROLE_USER")'),
        new Post(security: 'is_granted("ROLE_USER")', uriTemplate: '/missions/tasks/{id}/progress'),
        new Patch(security: 'is_granted("ROLE_USER")', uriTemplate: '/missions/tasks/{id}/claim'),
    ],
    normalizationContext: ['groups' => ['task:read']],
    denormalizationContext: ['groups' => ['task:write']]
)]
class MissionTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DailyMission::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DailyMission $dailyMission = null;

    #[ORM\Column(length: 100)]
    #[Groups(['task:read'])]
    private ?string $goal = null;

    #[ORM\Column]
    #[Groups(['task:read'])]
    private int $target = 0;

    #[ORM\Column]
    #[Groups(['task:read', 'task:write'])]
    private int $progress = 0;

    #[ORM\Column]
    #[Groups(['task:read'])]
    private array $reward = [];

    #[ORM\Column]
    #[Groups(['task:read'])]
    private bool $isClaimed = false;

    #[ORM\Column(length: 255)]
    #[Groups(['task:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['task:read'])]
    private ?string $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDailyMission(): ?DailyMission
    {
        return $this->dailyMission;
    }

    public function setDailyMission(?DailyMission $dailyMission): static
    {
        $this->dailyMission = $dailyMission;
        return $this;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(string $goal): static
    {
        $this->goal = $goal;
        return $this;
    }

    public function getTarget(): int
    {
        return $this->target;
    }

    public function setTarget(int $target): static
    {
        $this->target = $target;
        return $this;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): static
    {
        $this->progress = min($progress, $this->target);
        return $this;
    }

    public function getReward(): array
    {
        return $this->reward;
    }

    public function setReward(array $reward): static
    {
        $this->reward = $reward;
        return $this;
    }

    public function isClaimed(): bool
    {
        return $this->isClaimed;
    }

    public function setIsClaimed(bool $isClaimed): static
    {
        $this->isClaimed = $isClaimed;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
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

    public function isCompleted(): bool
    {
        return $this->progress >= $this->target;
    }
}
