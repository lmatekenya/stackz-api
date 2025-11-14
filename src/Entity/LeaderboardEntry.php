<?php

namespace App\Entity;

use App\Repository\LeaderboardEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: LeaderboardEntryRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['leaderboard:read']]
)]
class LeaderboardEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['leaderboard:read'])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'leaderboardEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['leaderboard:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column]
    #[Groups(['leaderboard:read'])]
    private int $score = 0;

//    #[ORM\Column(type: 'integer')]
//    private ?int $rank = null;

    #[ORM\Column]
    #[Groups(['leaderboard:read'])]
    private int $timeElapsed = 0;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['leaderboard:read'])]
    private ?string $quizTitle = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['leaderboard:read'])]
    private ?\DateTimeInterface $date = null;

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

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;
        return $this;
    }

//    public function getRank(): ?int
//    {
//        return $this->rank;
//    }
//
//    public function setRank(int $rank): static
//    {
//        $this->rank = $rank;
//        return $this;
//    }

    public function getTimeElapsed(): int
    {
        return $this->timeElapsed;
    }

    public function setTimeElapsed(int $timeElapsed): static
    {
        $this->timeElapsed = $timeElapsed;
        return $this;
    }

    public function getQuizTitle(): ?string
    {
        return $this->quizTitle;
    }

    public function setQuizTitle(string $quizTitle): static
    {
        $this->quizTitle = $quizTitle;
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
}
