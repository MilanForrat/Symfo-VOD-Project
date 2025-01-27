<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amountTotal = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $paymentId = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $isPaid = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalNumberOfOrders = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmountTotal(): ?float
    {
        return $this->amountTotal;
    }

    public function setAmountTotal(float $amountTotal): static
    {
        $this->amountTotal = $amountTotal;

        return $this;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): static
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getTotalNumberOfOrders(): ?int
    {
        return $this->totalNumberOfOrders;
    }

    public function setTotalNumberOfOrders(int $totalNumberOfOrders): static
    {
        $this->totalNumberOfOrders = $totalNumberOfOrders;

        return $this;
    }
}
