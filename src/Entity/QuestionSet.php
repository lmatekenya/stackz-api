<?php

namespace App\Entity;

use App\Repository\QuestionSetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: QuestionSetRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['question_set:read']]
)]
class QuestionSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['question_set:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['question_set:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Groups(['question_set:read'])]
    private ?string $format = 'standard';

    #[ORM\Column(type: 'json')]
    #[Groups(['question_set:read'])]
    private array $scoringRules = [];

    #[ORM\Column]
    #[Groups(['question_set:read'])]
    private int $basePoints = 100;

    #[ORM\Column(type: 'float')]
    #[Groups(['question_set:read'])]
    private float $timeBonusMultiplier = 1.0;

    #[ORM\OneToMany(mappedBy: 'questionSet', targetEntity: Question::class)]
    #[Groups(['question_set:read'])]
    private Collection $questions;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['question_set:read'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->scoringRules = [
            'base_points' => 100,
            'time_bonus' => true,
            'streak_bonus' => true,
            'combo_multiplier' => false
        ];
    }

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

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function getScoringRules(): array
    {
        return $this->scoringRules;
    }

    public function setScoringRules(array $scoringRules): static
    {
        $this->scoringRules = $scoringRules;
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

    public function getTimeBonusMultiplier(): float
    {
        return $this->timeBonusMultiplier;
    }

    public function setTimeBonusMultiplier(float $timeBonusMultiplier): static
    {
        $this->timeBonusMultiplier = $timeBonusMultiplier;
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
            $question->setQuestionSet($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuestionSet() === $this) {
                $question->setQuestionSet(null);
            }
        }
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
}
