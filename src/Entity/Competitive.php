<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class Competitive
{
    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $leaderboardPeriod = 'all'; // daily, weekly, monthly, all

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $leaderboardScope = 'global'; // global, friends, category

    public function getLeaderboardPeriod(): ?string
    {
        return $this->leaderboardPeriod;
    }

    public function setLeaderboardPeriod(?string $leaderboardPeriod): static
    {
        $this->leaderboardPeriod = $leaderboardPeriod;
        return $this;
    }

    public function getLeaderboardScope(): ?string
    {
        return $this->leaderboardScope;
    }

    public function setLeaderboardScope(?string $leaderboardScope): static
    {
        $this->leaderboardScope = $leaderboardScope;
        return $this;
    }
}
