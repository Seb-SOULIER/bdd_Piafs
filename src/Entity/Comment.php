<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Children $adherant = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $intervenant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddAt(): ?\DateTimeImmutable
    {
        return $this->addAt;
    }

    public function setAddAt(\DateTimeImmutable $addAt): self
    {
        $this->addAt = $addAt;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAdherant(): ?Children
    {
        return $this->adherant;
    }

    public function setAdherant(?Children $adherant): self
    {
        $this->adherant = $adherant;

        return $this;
    }

    public function getIntervenant(): ?User
    {
        return $this->intervenant;
    }

    public function setIntervenant(?User $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }
}
