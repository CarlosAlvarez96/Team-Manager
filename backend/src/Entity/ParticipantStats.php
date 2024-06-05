<?php

namespace App\Entity;

use App\Repository\ParticipantStatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantStatsRepository::class)]
class ParticipantStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participantStats')]
    private ?Participant $participant = null; // Corregido el nombre de la propiedad

    #[ORM\Column(nullable: true)]
    private ?int $goals = null;

    #[ORM\Column(nullable: true)]
    private ?int $assists = null;

    #[ORM\Column(nullable: true)]
    private ?int $yellow_cards = null;

    #[ORM\Column(nullable: true)]
    private ?int $red_cards = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function getGoals(): ?int
    {
        return $this->goals;
    }

    public function setGoals(?int $goals): self
    {
        $this->goals = $goals;

        return $this;
    }

    public function getAssists(): ?int
    {
        return $this->assists;
    }

    public function setAssists(?int $assists): self
    {
        $this->assists = $assists;

        return $this;
    }

    public function getYellowCards(): ?int
    {
        return $this->yellow_cards;
    }

    public function setYellowCards(?int $yellow_cards): self
    {
        $this->yellow_cards = $yellow_cards;

        return $this;
    }

    public function getRedCards(): ?int
    {
        return $this->red_cards;
    }

    public function setRedCards(?int $red_cards): self
    {
        $this->red_cards = $red_cards;

        return $this;
    }
}
