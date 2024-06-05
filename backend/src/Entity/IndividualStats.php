<?php

namespace App\Entity;

use App\Repository\IndividualStatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndividualStatsRepository::class)]
class IndividualStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $pace = null;

    #[ORM\Column(nullable: true)]
    private ?int $shooting = null;

    #[ORM\Column(nullable: true)]
    private ?int $physical = null;

    #[ORM\Column(nullable: true)]
    private ?int $defending = null;

    #[ORM\Column(nullable: true)]
    private ?int $dribbling = null;

    #[ORM\Column]
    private ?int $passing = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $position = null;

    #[ORM\OneToOne(inversedBy: 'individualStats', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $playerImageUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationImageUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clubImageUrl = null;

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPace(): ?int
    {
        return $this->pace;
    }

    public function setPace(?int $pace): self
    {
        $this->pace = $pace;

        return $this;
    }

    public function getShooting(): ?int
    {
        return $this->shooting;
    }

    public function setShooting(?int $shooting): self
    {
        $this->shooting = $shooting;

        return $this;
    }

    public function getPhysical(): ?int
    {
        return $this->physical;
    }

    public function setPhysical(?int $physical): self
    {
        $this->physical = $physical;

        return $this;
    }

    public function getDefending(): ?int
    {
        return $this->defending;
    }

    public function setDefending(?int $defending): self
    {
        $this->defending = $defending;

        return $this;
    }

    public function getDribbling(): ?int
    {
        return $this->dribbling;
    }

    public function setDribbling(?int $dribbling): self
    {
        $this->dribbling = $dribbling;

        return $this;
    }

    public function getPassing(): ?int
    {
        return $this->passing;
    }

    public function setPassing(int $passing): self
    {
        $this->passing = $passing;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPlayerImageUrl(): ?string
    {
        return $this->playerImageUrl;
    }

    public function setPlayerImageUrl(?string $playerImageUrl): static
    {
        $this->playerImageUrl = $playerImageUrl;

        return $this;
    }

    public function getNationImageUrl(): ?string
    {
        return $this->nationImageUrl;
    }

    public function setNationImageUrl(?string $nationImageUrl): static
    {
        $this->nationImageUrl = $nationImageUrl;

        return $this;
    }

    public function getClubImageUrl(): ?string
    {
        return $this->clubImageUrl;
    }

    public function setClubImageUrl(?string $clubImageUrl): static
    {
        $this->clubImageUrl = $clubImageUrl;

        return $this;
    }

    
}
