<?php

namespace App\Entity;

use App\Repository\RoadtripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoadtripRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Roadtrip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $cover_image = null;

    #[ORM\ManyToOne(inversedBy: 'roadtrips', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne(targetEntity: Vehicles::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicles $vehicle = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    /**
     * @var Collection<int, Checkpoint>
     */
    #[ORM\OneToMany(targetEntity: Checkpoint::class, mappedBy: 'roadtrip', cascade: ['persist', 'remove'])]
    public Collection $checkpoints;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->checkpoints = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->cover_image;
    }

    public function setCoverImage(string $cover_image): static
    {
        $this->cover_image = $cover_image;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getVehicle(): ?Vehicles
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicles $vehicle): static
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    /**
     * @return Collection<int, Checkpoint>
     */
    public function getCheckpoints(): Collection
    {
        return $this->checkpoints;
    }

    public function addCheckpoints(Checkpoint $checkpoints): static
    {
        if (!$this->checkpoints->contains($checkpoints)) {
            $this->checkpoints->add($checkpoints);
            $checkpoints->setRoadtrip($this);
        }

        return $this;
    }

    public function removeCheckpoints(Checkpoint $checkpoints): static
    {
        if ($this->checkpoints->removeElement($checkpoints)) {
            // set the owning side to null (unless already changed)
            if ($checkpoints->getRoadtrip() === $this) {
                $checkpoints->setRoadtrip(null);
            }
        }

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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updated_at = new \DateTimeImmutable();

        if ($this->created_at === null) {
            $this->created_at = new \DateTimeImmutable();
        }
    }
}
