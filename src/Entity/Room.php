<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: 'is_granted("ROLE_USER")'),
        new Post(security: 'is_granted("ROLE_USER")'),
        new Put(security: 'is_granted("ROLE_USER")'),
        new Delete(security: 'is_granted("ROLE_USER")'),
        new GetCollection(security: 'is_granted("ROLE_USER")'),
    ],
    normalizationContext: ['groups' => ['room:read']],
    denormalizationContext: ['groups' => ['room:write']]
)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['room:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Groups(['room:read', 'room:write'])]
    private ?string $code = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['room:read'])]
    private ?User $host = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'room_participants')]
    #[Groups(['room:read'])]
    private Collection $participants;

    #[ORM\ManyToMany(targetEntity: Quiz::class)]
    #[ORM\JoinTable(name: 'room_quizzes')]
    #[Groups(['room:read', 'room:write'])]
    private Collection $quizzes;

    #[ORM\Column(length: 20)]
    #[Groups(['room:read'])]
    private ?string $status = 'waiting'; // waiting, active, completed

    #[ORM\Column(type: 'json')]
    #[Groups(['room:read'])]
    private array $scores = [];

    #[ORM\Column]
    #[Groups(['room:read', 'room:write'])]
    private bool $isPublic = true;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['room:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['room:read'])]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['room:read'])]
    private ?\DateTimeInterface $endedAt = null;

    #[ORM\Column]
    #[Groups(['room:read', 'room:write'])]
    private int $maxParticipants = 8;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['room:read', 'room:write'])]
    private ?string $name = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->code = $this->generateRoomCode();
    }

    private function generateRoomCode(): string
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getHost(): ?User
    {
        return $this->host;
    }

    public function setHost(?User $host): static
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }
        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);
        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
        }
        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        $this->quizzes->removeElement($quiz);
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getScores(): array
    {
        return $this->scores;
    }

    public function setScores(array $scores): static
    {
        $this->scores = $scores;
        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;
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

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): static
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getMaxParticipants(): int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): static
    {
        $this->maxParticipants = $maxParticipants;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function canJoin(): bool
    {
        return $this->status === 'waiting' &&
            $this->participants->count() < $this->maxParticipants;
    }
}
