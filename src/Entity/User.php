<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a deja un compte avec cet email')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("user")]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups("user")]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups("user")]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("user")]
    private ?\DateTimeInterface $validToken = null;

    #[ORM\Column(length: 255)]
    #[Groups("user")]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups("user")]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups("user")]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("user")]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups("user")]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Groups("user")]
    private ?int $zipcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("user")]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("user")]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups("user")]
    private ?\DateTimeImmutable $subcribeAt;

    #[ORM\Column]
    #[Groups("user")]
    private ?bool $isActive = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $restoreCode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("user")]
    private ?\DateTimeInterface $activeAt = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Children::class)]
    private Collection $childrens;

    #[ORM\OneToMany(mappedBy: 'intervenant', targetEntity: Atelier::class)]
    private Collection $ateliers;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Actualite::class)]
    private Collection $actualites;

    public function __construct()
    {
        $this->subcribeAt = new DateTimeImmutable('now');
        $this->childrens = new ArrayCollection();
        $this->ateliers = new ArrayCollection();
        $this->atelierParticipant = new ArrayCollection();
        $this->actualites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getValidToken(): ?\DateTimeInterface
    {
        return $this->validToken;
    }

    public function setValidToken(?\DateTimeInterface $validToken): self
    {
        $this->validToken = $validToken;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(?int $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSubcribeAt(): ?\DateTimeImmutable
    {
        return $this->subcribeAt;
    }

    public function setSubcribeAt(\DateTimeImmutable $subcribeAt): self
    {
        $this->subcribeAt = $subcribeAt;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRestoreCode(): ?string
    {
        return $this->restoreCode;
    }

    public function setRestoreCode(?string $restoreCode): self
    {
        $this->restoreCode = $restoreCode;

        return $this;
    }

    public function getActiveAt(): ?\DateTimeInterface
    {
        return $this->activeAt;
    }

    public function setActiveAt(?\DateTimeInterface $activeAt): self
    {
        $this->activeAt = $activeAt;

        return $this;
    }

    /**
     * @return Collection<int, Children>
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChildren(Children $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens->add($children);
            $children->setParent($this);
        }

        return $this;
    }

    public function removeChildren(Children $children): self
    {
        if ($this->childrens->removeElement($children)) {
            // set the owning side to null (unless already changed)
            if ($children->getParent() === $this) {
                $children->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Atelier>
     */
    public function getAteliers(): Collection
    {
        return $this->ateliers;
    }

    public function addAtelier(Atelier $atelier): self
    {
        if (!$this->ateliers->contains($atelier)) {
            $this->ateliers->add($atelier);
            $atelier->setIntervenant($this);
        }

        return $this;
    }

    public function removeAtelier(Atelier $atelier): self
    {
        if ($this->ateliers->removeElement($atelier)) {
            // set the owning side to null (unless already changed)
            if ($atelier->getIntervenant() === $this) {
                $atelier->setIntervenant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Actualite>
     */
    public function getActualites(): Collection
    {
        return $this->actualites;
    }

    public function addActualite(Actualite $actualite): self
    {
        if (!$this->actualites->contains($actualite)) {
            $this->actualites->add($actualite);
            $actualite->setAuthor($this);
        }

        return $this;
    }

    public function removeActualite(Actualite $actualite): self
    {
        if ($this->actualites->removeElement($actualite)) {
            // set the owning side to null (unless already changed)
            if ($actualite->getAuthor() === $this) {
                $actualite->setAuthor(null);
            }
        }

        return $this;
    }
}
