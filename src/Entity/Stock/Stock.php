<?php

namespace App\Entity\Stock;

use App\Entity\EntityInterface;
use App\Repository\Stock\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = null;

    #[ORM\OneToOne(inversedBy: 'stock', targetEntity: Equipment::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    #[ORM\OneToMany(mappedBy: 'stock', targetEntity: StockHistory::class, cascade: ['persist', 'remove'])]
    private Collection $stockHistories;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeInterface $archivedAt = null;

    public function __construct()
    {
        $this->stockHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function addQuantity(int $quantity): self
    {
        $this->quantity += $quantity;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(Equipment $equipment): self
    {
        $this->equipment = $equipment;

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
            $stockHistory->setStock($this);
        }

        return $this;
    }

    public function removeStockHistory(StockHistory $stockHistory): self
    {
        if ($this->stockHistories->removeElement($stockHistory)) {
            // set the owning side to null (unless already changed)
            if ($stockHistory->getStock() === $this) {
                $stockHistory->setStock(null);
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
     * @return Stock
     */
    public function setArchivedAt(?\DateTimeInterface $archivedAt): Stock
    {
        $this->archivedAt = $archivedAt;
        $this->equipment->setArchivedAt($archivedAt);

        return $this;
    }
}
