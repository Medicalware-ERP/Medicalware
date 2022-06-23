<?php

namespace App\Entity\Accounting;

use App\Entity\Stock\Equipment;
use App\Repository\Accounting\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = null;

    #[ORM\Column(type: 'integer')]
    private ?int $ht = null;

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    #[ORM\Column(type: 'float')]
    private ?float $equipmentPrice = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $equipmentName = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $equipmentReference = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int|null $quantity
     * @return OrderLine
     */
    public function setQuantity(?int $quantity): OrderLine
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHt(): ?int
    {
        return $this->ht;
    }

    /**
     * @param int|null $ht
     * @return OrderLine
     */
    public function setHt(?int $ht): OrderLine
    {
        $this->ht = $ht;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;
        $this->equipmentName = $equipment?->getName();
        $this->equipmentReference = $equipment?->getReference();
        $this->equipmentPrice = $equipment?->getPrice();

        return $this;
    }

    public function getEquipmentPrice(): ?float
    {
        return $this->equipmentPrice;
    }

    public function setEquipmentPrice(float $equipmentPrice): self
    {
        $this->equipmentPrice = $equipmentPrice;

        return $this;
    }

    public function getEquipmentName(): ?string
    {
        return $this->equipmentName;
    }

    public function setEquipmentName(string $equipmentName): self
    {
        $this->equipmentName = $equipmentName;

        return $this;
    }

    public function getEquipmentReference(): ?string
    {
        return $this->equipmentReference;
    }

    public function setEquipmentReference(string $equipmentReference): self
    {
        $this->equipmentReference = $equipmentReference;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function calculateHt(): int
    {
        $this->setHt($this->getPrice() * $this->getQuantity());
        return $this->getHt();
    }
}
