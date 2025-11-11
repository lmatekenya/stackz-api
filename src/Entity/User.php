<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(security: 'is_granted("ROLE_USER") and object == user'),
        new Put(security: 'is_granted("ROLE_USER") and object == user'),
        new Patch(security: 'is_granted("ROLE_USER") and object == user'),
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $username = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['user:read'])]
    private int $level = 1;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['user:read'])]
    private int $totalPoints = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['user:read'])]
    private int $quizzesCompleted = 0;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['user:read', 'user:write'])]
    private array $preferences = [];

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['user:read'])]
    private int $xp = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['user:read'])]
    private int $currencyBalance = 0;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['user:read'])]
    private int $streakCount = 0;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['user:read'])]
    private array $categoryStats = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LeaderboardEntry::class)]
    private Collection $leaderboardEntries;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ActivityLog::class)]
    private Collection $activityLogs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CurrencyTransaction::class)]
    private Collection $currencyTransactions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserBadge::class)]
    private Collection $userBadges;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserTitle::class)]
    private Collection $userTitles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: QuizSession::class)]
    private Collection $quizSessions;

    public function __construct()
    {
        $this->leaderboardEntries = new ArrayCollection();
        $this->activityLogs = new ArrayCollection();
        $this->currencyTransactions = new ArrayCollection();
        $this->userBadges = new ArrayCollection();
        $this->userTitles = new ArrayCollection();
        $this->quizSessions = new ArrayCollection();
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;
        return $this;
    }

    public function getTotalPoints(): int
    {
        return $this->totalPoints;
    }

    public function setTotalPoints(int $totalPoints): static
    {
        $this->totalPoints = $totalPoints;
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

    public function getPreferences(): array
    {
        return $this->preferences;
    }

    public function setPreferences(array $preferences): static
    {
        $this->preferences = $preferences;
        return $this;
    }

    public function getXp(): int
    {
        return $this->xp;
    }

    public function setXp(int $xp): static
    {
        $this->xp = $xp;
        return $this;
    }

    public function addXp(int $amount): static
    {
        $this->xp += $amount;
        return $this;
    }

    public function getCurrencyBalance(): int
    {
        return $this->currencyBalance;
    }

    public function setCurrencyBalance(int $currencyBalance): static
    {
        $this->currencyBalance = $currencyBalance;
        return $this;
    }

    public function addCurrencyBalance(int $amount): static
    {
        $this->currencyBalance += $amount;
        return $this;
    }

    public function getStreakCount(): int
    {
        return $this->streakCount;
    }

    public function setStreakCount(int $streakCount): static
    {
        $this->streakCount = $streakCount;
        return $this;
    }

    public function getCategoryStats(): array
    {
        return $this->categoryStats;
    }

    public function setCategoryStats(array $categoryStats): static
    {
        $this->categoryStats = $categoryStats;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    //method to fix the UserInterface requirement
    public function getSalt(): ?string
    {
        // Not needed when using bcrypt
        return null;
    }

    /**
     * @return Collection<int, QuizSession>
     */
    public function getQuizSessions(): Collection
    {
        return $this->quizSessions;
    }

    public function addQuizSession(QuizSession $quizSession): static
    {
        if (!$this->quizSessions->contains($quizSession)) {
            $this->quizSessions->add($quizSession);
            $quizSession->setUser($this);
        }
        return $this;
    }

    public function removeQuizSession(QuizSession $quizSession): static
    {
        if ($this->quizSessions->removeElement($quizSession)) {
            // set the owning side to null (unless already changed)
            if ($quizSession->getUser() === $this) {
                $quizSession->setUser(null);
            }
        }
        return $this;
    }

}
