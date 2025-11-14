<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['quiz:read']]
)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['quiz:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['quiz:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['quiz:read'])]
    private ?string $category = null;

    #[ORM\Column(length: 50)]
    #[Groups(['quiz:read'])]
    private ?string $difficulty = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['quiz:read'])]
    private array $gradient = [];

    #[ORM\Column(length: 255)]
    #[Groups(['quiz:read'])]
    private ?string $icon = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['quiz:read'])]
    private array $tags = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['quiz:read'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Question::class, cascade: ['persist'])]
    #[Groups(['quiz:read'])]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: QuizSession::class)]
    private Collection $quizSessions;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['quiz:read'])]
    private int $basePoints = 100;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['quiz:read'])]
    private int $timeLimit = 300;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->quizSessions = new ArrayCollection();
    }

    // Getters and setters...
    public function getId(): ?int
    {
        return $this->id;
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    public function getGradient(): array
    {
        return $this->gradient;
    }

    public function setGradient(array $gradient): static
    {
        $this->gradient = $gradient;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): static
    {
        $this->tags = $tags;
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

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }
        return $this;
    }

    public function getBasePoints(): int
    {
        return $this->basePoints;
    }

    public function setBasePoints(int $basePoints): static
    {
        $this->basePoints = $basePoints;
        return $this;
    }

    public function getTimeLimit(): int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(int $timeLimit): static
    {
        $this->timeLimit = $timeLimit;
        return $this;
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
            $quizSession->setQuiz($this);
        }
        return $this;
    }

    public function removeQuizSession(QuizSession $quizSession): static
    {
        if ($this->quizSessions->removeElement($quizSession)) {
            // set the owning side to null (unless already changed)
            if ($quizSession->getQuiz() === $this) {
                $quizSession->setQuiz(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? 'Untitled Quiz';
    }

}
