<?php

namespace App\Entity;

use App\Repository\QuizSessionRepository;
use App\State\QuizSessionProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: QuizSessionRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            processor: QuizSessionProcessor::class
        ),
    ],
    normalizationContext: ['groups' => ['quiz_session:read']],
    denormalizationContext: ['groups' => ['quiz_session:write']]
)]
class QuizSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['quiz_session:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'quizSessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['quiz_session:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'quizSessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['quiz_session:read', 'quiz_session:write'])]
    private ?Quiz $quiz = null;

    #[ORM\Column]
    #[Groups(['quiz_session:read', 'quiz_session:write'])]
    private int $score = 0;

    #[ORM\Column]
    #[Groups(['quiz_session:read', 'quiz_session:write'])]
    private int $totalQuestions = 0;

    #[ORM\Column]
    #[Groups(['quiz_session:read', 'quiz_session:write'])]
    private int $correctAnswers = 0;

    #[ORM\Column]
    #[Groups(['quiz_session:read', 'quiz_session:write'])]
    private int $timeElapsed = 0;

    #[ORM\Column]
    #[Groups(['quiz_session:read', 'quiz_session:write'])]
    private bool $cleared = false;

    #[ORM\Column]
    #[Groups(['quiz_session:read'])]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column]
    #[Groups(['quiz_session:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getTotalQuestions(): int
    {
        return $this->totalQuestions;
    }

    public function setTotalQuestions(int $totalQuestions): self
    {
        $this->totalQuestions = $totalQuestions;
        return $this;
    }

    public function getCorrectAnswers(): int
    {
        return $this->correctAnswers;
    }

    public function setCorrectAnswers(int $correctAnswers): self
    {
        $this->correctAnswers = $correctAnswers;
        return $this;
    }

    public function getTimeElapsed(): int
    {
        return $this->timeElapsed;
    }

    public function setTimeElapsed(int $timeElapsed): self
    {
        $this->timeElapsed = $timeElapsed;
        return $this;
    }

    public function isCleared(): bool
    {
        return $this->cleared;
    }

    public function setCleared(bool $cleared): self
    {
        $this->cleared = $cleared;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
