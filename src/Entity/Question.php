<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['quiz:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\ManyToOne(targetEntity: QuestionSet::class, inversedBy: 'questions')]
    private ?QuestionSet $questionSet = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['quiz:read'])]
    private ?string $text = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['quiz:read'])]
    private array $options = [];

    #[ORM\Column(length: 255)]
    #[Groups(['quiz:read'])]
    private ?string $correctAnswer = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['quiz:read'])]
    private ?array $correctAnswers = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['quiz:read'])]
    private ?int $timeLimit = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['quiz:read'])]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['quiz:read'])]
    private ?string $audioUrl = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['quiz:read'])]
    private ?string $type = 'multiple_choice';

    public function getId(): ?int
    {
        return $this->id;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getCorrectAnswer(): ?string
    {
        return $this->correctAnswer;
    }

    public function setCorrectAnswer(string $correctAnswer): static
    {
        $this->correctAnswer = $correctAnswer;
        return $this;
    }

    public function getCorrectAnswers(): ?array
    {
        return $this->correctAnswers;
    }

    public function setCorrectAnswers(?array $correctAnswers): static
    {
        $this->correctAnswers = $correctAnswers;
        return $this;
    }

    public function getTimeLimit(): ?int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(?int $timeLimit): static
    {
        $this->timeLimit = $timeLimit;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getAudioUrl(): ?string
    {
        return $this->audioUrl;
    }

    public function setAudioUrl(?string $audioUrl): static
    {
        $this->audioUrl = $audioUrl;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getQuestionSet(): ?QuestionSet
    {
        return $this->questionSet;
    }

    public function setQuestionSet(?QuestionSet $questionSet): static
    {
        $this->questionSet = $questionSet;
        return $this;
    }
}
