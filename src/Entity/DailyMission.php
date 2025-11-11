<?php

namespace App\Entity;

use App\Repository\DailyMissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;

#[ORM\Entity(repositoryClass: DailyMissionRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: 'is_granted("ROLE_USER")'),
        new Patch(security: 'is_granted("ROLE_USER")'),
    ],
    normalizationContext: ['groups' => ['mission:read']],
    denormalizationContext: ['groups' => ['mission:write']]
)]
class DailyMission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['mission:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'date')]
    #[Groups(['mission:read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Groups(['mission:read', 'mission:write'])]
    private int $quizzesCompleted = 0;

    #[ORM\Column]
    #[Groups(['mission:read'])]
    private int $target = 5;

    #[ORM\Column]
    #[Groups(['mission:read'])]
    private int $streak = 0;

    #[ORM\Column]
    #[Groups(['mission:read'])]
    private int $reward = 100;

    #[ORM\OneToMany(mappedBy: 'dailyMission', targetEntity: MissionTask::class, cascade: ['persist'])]
    #[Groups(['mission:read'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getQuizzesCompleted(): int
    {
        return $this->quizzesCompleted;
    }

    public function setQuizzesCompleted(int $quizzesCompleted): static
    {
        $this->quizzesCompleted = $quizzesCompleted;
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

    public function getStreak(): int
    {
        return $this->streak;
    }

    public function setStreak(int $streak): static
    {
        $this->streak = $streak;
        return $this;
    }

    public function getReward(): int
    {
        return $this->reward;
    }

    public function setReward(int $reward): static
    {
        $this->reward = $reward;
        return $this;
    }

    /**
     * @return Collection<int, MissionTask>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(MissionTask $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setDailyMission($this);
        }
        return $this;
    }

    public function removeTask(MissionTask $task): static
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getDailyMission() === $this) {
                $task->setDailyMission(null);
            }
        }
        return $this;
    }
}
