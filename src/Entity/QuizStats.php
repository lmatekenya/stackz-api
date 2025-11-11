<?php

namespace App\Entity;

use App\Repository\QuizStatsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QuizStatsRepository::class)]
class QuizStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column]
    private int $attempts = 0;

    #[ORM\Column]
    private int $clears = 0;

    #[ORM\Column]
    private int $totalTime = 0;

    #[ORM\Column]
    private int $totalScore = 0;

    #[ORM\Column]
    private int $bestScore = 0;

    #[ORM\Column]
    private int $fastestTime = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $lastPlayed = null;

    #[ORM\Column(type: 'float')]
    private float $avgTime = 0.0;

    #[ORM\Column(type: 'float')]
    private float $winRate = 0.0;

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

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): static
    {
        $this->attempts = $attempts;
        return $this;
    }

    public function getClears(): int
    {
        return $this->clears;
    }

    public function setClears(int $clears): static
    {
        $this->clears = $clears;
        return $this;
    }

    public function getTotalTime(): int
    {
        return $this->totalTime;
    }

    public function setTotalTime(int $totalTime): static
    {
        $this->totalTime = $totalTime;
        return $this;
    }

    public function getTotalScore(): int
    {
        return $this->totalScore;
    }

    public function setTotalScore(int $totalScore): static
    {
        $this->totalScore = $totalScore;
        return $this;
    }

    public function getBestScore(): int
    {
        return $this->bestScore;
    }

    public function setBestScore(int $bestScore): static
    {
        $this->bestScore = $bestScore;
        return $this;
    }

    public function getFastestTime(): int
    {
        return $this->fastestTime;
    }

    public function setFastestTime(int $fastestTime): static
    {
        $this->fastestTime = $fastestTime;
        return $this;
    }

    public function getLastPlayed(): ?\DateTimeInterface
    {
        return $this->lastPlayed;
    }

    public function setLastPlayed(?\DateTimeInterface $lastPlayed): static
    {
        $this->lastPlayed = $lastPlayed;
        return $this;
    }

    public function getAvgTime(): float
    {
        return $this->avgTime;
    }

    public function setAvgTime(float $avgTime): static
    {
        $this->avgTime = $avgTime;
        return $this;
    }

    public function getWinRate(): float
    {
        return $this->winRate;
    }

    public function setWinRate(float $winRate): static
    {
        $this->winRate = $winRate;
        return $this;
    }
}
