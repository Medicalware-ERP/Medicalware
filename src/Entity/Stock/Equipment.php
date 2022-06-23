<?php

namespace App\Entity\Stock;

use App\Entity\Accounting\OrderLine;
use App\Entity\Provider;
use App\Entity\Service;
use App\Repository\Stock\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[UniqueEntity('reference')]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'equipments')]
    private Collection $services;

    #[ORM\OneToOne(mappedBy: 'equipment', targetEntity: Stock::class, cascade: ['persist', 'remove'])]
    private ?Stock $stock = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: OrderLine::class)]
    private Collection $orderLines;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeInterface $archivedAt = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
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

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        $this->services->removeElement($service);

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): self
    {
        // set the owning side of the relation if necessary
        if ($stock->getEquipment() !== $this) {
            $stock->setEquipment($this);
        }

        $this->stock = $stock;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines[] = $orderLine;
            $orderLine->setEquipment($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getEquipment() === $this) {
                $orderLine->setEquipment(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getArchivedAt(): ?\DateTimeInterface
    {
        return $this->archivedAt;
    }

    /**
     * @param \DateTimeInterface|null $archivedAt
     * @return Equipment
     */
    public function setArchivedAt(?\DateTimeInterface $archivedAt): Equipment
    {
        $this->archivedAt = $archivedAt;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
