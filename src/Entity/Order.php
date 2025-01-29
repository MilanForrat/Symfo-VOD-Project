<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    /*
    * 1 : en attente de paiement
    * 2 : paiement validé
    */ 
    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $addressFacturation = null;

    /**
     * @var Collection<int, OrderDetail>
     */
    #[ORM\OneToMany(targetEntity: OrderDetail::class, mappedBy: 'myOrder', cascade:['persist'])]
    private Collection $orderDetails;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

    // fonction pour récupéré le total TTC
    public function getTotalTTC(){
        $products = $this->getOrderDetails();
        $totalTTC = 0;

        foreach($products as $product){
            // dd($product);
            // on récupère le coefficient de tva et on le multiplie ce qui donne par ex: 1.2 pour 20% TVA
            $coeff = 1+ ($product->getProductTva() / 100);
            // nous renvoie le total de tva
            $totalTTC += $product->getProductPrice()*$coeff;

        }

        return  $totalTTC;
    }
    // fonction pour récupéré le total de la TVA
    public function getTotalTVA(){
            $products = $this->getOrderDetails();
            $totalTVA = 0;
    
            foreach($products as $product){
                // dd($product);
                // on récupère le coefficient de tva
                $coeff = $product->getProductTva() / 100;
                // nous renvoie le total de tva
                $totalTVA += $product->getProductPrice()*$coeff;
    
            }
    
            return  $totalTVA;
    }

    // fonction pour récupéré le total hors taxe
    public function getTotalHT(){
        $products = $this->getOrderDetails();
        $totalHT = 0;

        foreach($products as $product){
            // dd($product);
            // on récupère le coefficient de tva
            $coeff = $product->getProductPrice();
            // nous renvoie le total de tva
            $totalHT += $coeff;

        }

        return  $totalHT;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAddressFacturation(): ?string
    {
        return $this->addressFacturation;
    }

    public function setAddressFacturation(?string $addressFacturation): static
    {
        $this->addressFacturation = $addressFacturation;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setMyOrder($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getMyOrder() === $this) {
                $orderDetail->setMyOrder(null);
            }
        }

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
}
