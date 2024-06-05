<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?Game $game = null;

    #[ORM\OneToMany(mappedBy: 'partipant', targetEntity: ParticipantStats::class)]
    private Collection $participantStats;

    public function __construct()
    {
        $this->participantStats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection<int, ParticipantStats>
     */
    public function getParticipantStats(): Collection
    {
        return $this->participantStats;
    }

    public function addParticipantStat(ParticipantStats $participantStat): self
    {
        if (!$this->participantStats->contains($participantStat)) {
            $this->participantStats->add($participantStat);
            $participantStat->setPartipant($this);
        }

        return $this;
    }

    public function removeParticipantStat(ParticipantStats $participantStat): self
    {
        if ($this->participantStats->removeElement($participantStat)) {
            // set the owning side to null (unless already changed)
            if ($participantStat->getPartipant() === $this) {
                $participantStat->setPartipant(null);
            }
        }

        return $this;
    }
}
