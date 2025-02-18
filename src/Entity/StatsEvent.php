<?php

namespace App\Entity;

use App\Repository\StatsEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatsEventRepository::class)]
class StatsEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $event_id = null;

    #[ORM\Column(length: 255)]
    private ?string $event_name = null;

    #[ORM\Column]
    private ?int $play_count = null;

    #[ORM\Column]
    private ?int $NoFoodStats = null;

    #[ORM\Column]
    private ?int $WithFoodStats = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventId(): ?int
    {
        return $this->event_id;
    }

    public function setEventId(int $event_id): static
    {
        $this->event_id = $event_id;

        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->event_name;
    }

    public function setEventName(string $event_name): static
    {
        $this->event_name = $event_name;

        return $this;
    }

    public function getPlayCount(): ?int
    {
        return $this->play_count;
    }

    public function setPlayCount(int $play_count): static
    {
        $this->play_count = $play_count;

        return $this;
    }

    public function getNoFoodStats(): ?int
    {
        return $this->NoFoodStats;
    }

    public function setNoFoodStats(int $NoFoodStats): static
    {
        $this->NoFoodStats = $NoFoodStats;

        return $this;
    }

    public function getWithFoodStats(): ?int
    {
        return $this->WithFoodStats;
    }

    public function setWithFoodStats(int $WithFoodStats): static
    {
        $this->WithFoodStats = $WithFoodStats;

        return $this;
    }
}
