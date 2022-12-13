<?php

namespace App\Entity;

use App\Repository\AtelierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AtelierRepository::class)]
class Atelier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hourStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hourStop = null;

    #[ORM\Column(nullable: true)]
    private ?int $place = null;

    #[ORM\ManyToOne(inversedBy: 'ateliers')]
    private ?user $intervenant = null;

    #[ORM\ManyToMany(targetEntity: user::class, inversedBy: 'atelierParticipant')]
    private Collection $participant;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->participant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHourStart(): ?\DateTimeInterface
    {
        return $this->hourStart;
    }

    public function setHourStart(?\DateTimeInterface $hourStart): self
    {
        $this->hourStart = $hourStart;

        return $this;
    }

    public function getHourStop(): ?\DateTimeInterface
    {
        return $this->hourStop;
    }

    public function setHourStop(?\DateTimeInterface $hourStop): self
    {
        $this->hourStop = $hourStop;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(?int $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getIntervenant(): ?user
    {
        return $this->intervenant;
    }

    public function setIntervenant(?user $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(user $participant): self
    {
        if (!$this->participant->contains($participant)) {
            $this->participant->add($participant);
        }

        return $this;
    }

    public function removeParticipant(user $participant): self
    {
        $this->participant->removeElement($participant);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
