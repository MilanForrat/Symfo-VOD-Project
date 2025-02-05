<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reservationDateEnd = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $eventDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $eventPriceNoFood = null;

    #[ORM\Column]
    private ?string $eventPriceWithFood = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $isHomepage = null;


        // utilisée pour easyadmin crud
    public function __toString()
    {
        return 
            $this->name;
            $this->eventPriceNoFood;
            $this->eventPriceWithFood;
        ;
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

    public function getReservationDateEnd(): ?\DateTimeInterface
    {
        return $this->reservationDateEnd;
    }

    public function setReservationDateEnd(\DateTimeInterface $reservationDateEnd): static
    {
        $this->reservationDateEnd = $reservationDateEnd;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTimeInterface $eventDate): static
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEventPriceNoFood(): ?string
    {
        return $this->eventPriceNoFood;
    }

    public function setEventPriceNoFood(string $eventPriceNoFood): static
    {
        $this->eventPriceNoFood = $eventPriceNoFood;

        return $this;
    }

    public function getEventPriceWithFood(): ?string
    {
        return $this->eventPriceWithFood;
    }

    public function setEventPriceWithFood(string $eventPriceWithFood): static
    {
        $this->eventPriceWithFood = $eventPriceWithFood;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function isHomepage(): ?bool
    {
        return $this->isHomepage;
    }

    public function setIsHomepage(bool $isHomepage): static
    {
        $this->isHomepage = $isHomepage;

        return $this;
    }

}
