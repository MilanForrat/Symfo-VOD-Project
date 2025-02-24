<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'season')]
    private Collection $Year;

    #[ORM\Column(length: 255)]
    private ?string $seasonYear = null;

    public function __construct()
    {
        $this->Year = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->seasonYear;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getYear(): Collection
    {
        return $this->Year;
    }

    public function addYear(Video $year): static
    {
        if (!$this->Year->contains($year)) {
            $this->Year->add($year);
            $year->setSeason($this);
        }

        return $this;
    }

    public function removeYear(Video $year): static
    {
        if ($this->Year->removeElement($year)) {
            // set the owning side to null (unless already changed)
            if ($year->getSeason() === $this) {
                $year->setSeason(null);
            }
        }

        return $this;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeasonYear(): ?string
    {
        return $this->seasonYear;
    }

    public function setSeasonYear(string $seasonYear): static
    {
        $this->seasonYear = $seasonYear;

        return $this;
    }

}
