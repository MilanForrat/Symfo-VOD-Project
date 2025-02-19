<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $event_id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $order_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $boughtDate = null;

    #[ORM\Column]
    private ?int $numberOfTickets = null;

    #[ORM\Column]
    private ?int $numberOfTicketsNoFood = null;

    #[ORM\Column]
    private ?int $numberOfTicketsWithFood = null;

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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getOrderId(): ?int
    {
        return $this->order_id;
    }

    public function setOrderId(int $order_id): static
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getBoughtDate(): ?\DateTimeInterface
    {
        return $this->boughtDate;
    }

    public function setBoughtDate(\DateTimeInterface $boughtDate): static
    {
        $this->boughtDate = $boughtDate;

        return $this;
    }

    public function getNumberOfTickets(): ?int
    {
        return $this->numberOfTickets;
    }

    public function setNumberOfTickets(int $numberOfTickets): static
    {
        $this->numberOfTickets = $numberOfTickets;

        return $this;
    }

    public function getNumberOfTicketsNoFood(): ?int
    {
        return $this->numberOfTicketsNoFood;
    }

    public function setNumberOfTicketsNoFood(int $numberOfTicketsNoFood): static
    {
        $this->numberOfTicketsNoFood = $numberOfTicketsNoFood;

        return $this;
    }

    public function getNumberOfTicketsWithFood(): ?int
    {
        return $this->numberOfTicketsWithFood;
    }

    public function setNumberOfTicketsWithFood(int $numberOfTicketsWithFood): static
    {
        $this->numberOfTicketsWithFood = $numberOfTicketsWithFood;

        return $this;
    }
}
