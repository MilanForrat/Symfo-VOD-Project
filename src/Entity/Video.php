<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $trailer = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\Column(length: 255)]
    private ?string $length = null;


    #[ORM\ManyToOne(inversedBy: 'videos')]
    private ?Category $category = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'video', orphanRemoval: true, cascade: ['persist'])]
    private Collection $user;

    // #[ORM\Column(type: Types::TIME_MUTABLE)]
    // private ?\DateTimeInterface $length = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $uploadedDate = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    private ?Language $language = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\Column]
    private ?float $tva = null;

    // fonction pour récupéré prix TTC selon la tva choisie en BDD
    public function getPriceTvaCalculator(){
        $coeff= 1 +($this->tva/100);
        $result=$coeff*$this->price;
        $result=$result-$this->price;
        return $result;
    }

    // fonction pour récupéré le total TTC
    public function getVideoTTC(){;
        $videoTTC = 0;

        $coeff = 1+ ($this->getTva() / 100);
        // nous renvoie le total de tva
        $videoTTC += $this->getPrice()*$coeff;


        return  $videoTTC;
    }

    // utilisée pour easyadmin crud
    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->user = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): static
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getTrailer(): ?string
    {
        return $this->trailer;
    }

    public function setTrailer(string $trailer): static
    {
        $this->trailer = $trailer;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setVideo($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getVideo() === $this) {
                $user->setVideo(null);
            }
        }

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(string $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getuploadedDate(): ?\DateTimeInterface
    {
        return $this->uploadedDate;
    }

    public function setuploadedDate(\DateTimeInterface $uploadedDate): static
    {
        $this->uploadedDate = $uploadedDate;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

}
