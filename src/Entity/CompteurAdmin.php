<?php

namespace App\Entity;

use App\Repository\CompteurAdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteurAdminRepository::class)]
class CompteurAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $countUploadApp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountUploadApp(): ?int
    {
        return $this->countUploadApp;
    }

    public function setCountUploadApp(?int $countUploadApp): self
    {
        $this->countUploadApp = $countUploadApp;

        return $this;
    }
}
