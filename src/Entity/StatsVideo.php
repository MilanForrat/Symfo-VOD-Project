<?php

namespace App\Entity;

use App\Repository\StatsVideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatsVideoRepository::class)]
class StatsVideo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $video_id = null;

    #[ORM\Column(length: 255)]
    private ?string $video_name = null;

    #[ORM\Column]
    private ?int $play_count = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideoId(): ?int
    {
        return $this->video_id;
    }

    public function setVideoId(int $video_id): static
    {
        $this->video_id = $video_id;

        return $this;
    }

    public function getVideoName(): ?string
    {
        return $this->video_name;
    }

    public function setVideoName(string $video_name): static
    {
        $this->video_name = $video_name;

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
}
