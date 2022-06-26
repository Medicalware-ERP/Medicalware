<?php

namespace App\Entity;

use App\Entity\Accounting\Invoice;
use App\Entity\Stock\StockHistory;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[InheritanceType("JOINED")]
#[DiscriminatorColumn(name : "discr", type : "string")]
#[DiscriminatorMap(["doctor" => "Doctor", "user" => "User"])]

class User extends Person implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface
{
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * The hashed password
     */
    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $leftAt = null;

    #[ORM\ManyToOne(targetEntity: UserType::class, cascade: ['persist'], inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserType $profession = null;

    #[ORM\OneToMany(mappedBy: 'validatedBy', targetEntity: Invoice::class)]
    private Collection $invoicesValidated;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: StockHistory::class)]
    private Collection $stockHistories;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $activatedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->invoicesValidated = new ArrayCollection();
        $this->stockHistories = new ArrayCollection();
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLeftAt(): ?\DateTimeImmutable
    {
        return $this->leftAt;
    }

    public function setLeftAt(?\DateTimeImmutable $leftAt): self
    {
        $this->leftAt = $leftAt;

        return $this;
    }

    public function getProfession(): ?UserType
    {
        return $this->profession;
    }

    public function setProfession(?UserType $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoicesValidated(): Collection
    {
        return $this->invoicesValidated;
    }

    public function addInvoicesValidated(Invoice $invoicesValidated): self
    {
        if (!$this->invoicesValidated->contains($invoicesValidated)) {
            $this->invoicesValidated[] = $invoicesValidated;
            $invoicesValidated->setValidatedBy($this);
        }

        return $this;
    }

    public function removeInvoicesValidated(Invoice $invoicesValidated): self
    {
        if ($this->invoicesValidated->removeElement($invoicesValidated)) {
            // set the owning side to null (unless already changed)
            if ($invoicesValidated->getValidatedBy() === $this) {
                $invoicesValidated->setValidatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StockHistory>
     */
    public function getStockHistories(): Collection
    {
        return $this->stockHistories;
    }

    public function addStockHistory(StockHistory $stockHistory): self
    {
        if (!$this->stockHistories->contains($stockHistory)) {
            $this->stockHistories[] = $stockHistory;
            $stockHistory->setUser($this);
        }

        return $this;
    }

    public function removeStockHistory(StockHistory $stockHistory): self
    {
        if ($this->stockHistories->removeElement($stockHistory)) {
            // set the owning side to null (unless already changed)
            if ($stockHistory->getUser() === $this) {
                $stockHistory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getActivatedAt(): ?DateTimeImmutable
    {
        return $this->activatedAt;
    }

    /**
     * @param DateTimeImmutable|null $activatedAt
     * @return User
     */
    public function setActivatedAt(?DateTimeImmutable $activatedAt): User
    {
        $this->activatedAt = $activatedAt;
        return $this;
    }
}
