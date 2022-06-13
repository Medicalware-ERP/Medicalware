<?php

namespace App\Entity\Accounting;

use App\Entity\Provider;
use App\Entity\Tva;
use App\Repository\Accounting\OrderRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $reference = null;

    #[ORM\Column(type: 'float')]
    private ?float $ht = null;

    #[ORM\Column(type: 'float')]
    private ?float $ttc = null;

    #[ORM\ManyToOne(targetEntity: Tva::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tva $tva = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $deliveryDate = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $deliveryPlannedDate = null;

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

    /**
     * @return float|null
     */
    public function getHt(): ?float
    {
        return $this->ht;
    }

    /**
     * @param float|null $ht
     */
    public function setHt(?float $ht): void
    {
        $this->ht = $ht;
    }

    /**
     * @return float|null
     */
    public function getTtc(): ?float
    {
        return $this->ttc;
    }

    /**
     * @param float|null $ttc
     */
    public function setTtc(?float $ttc): void
    {
        $this->ttc = $ttc;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): self
    {
        $this->tva = $tva;

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

    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getDeliveryPlannedDate(): ?DateTimeInterface
    {
        return $this->deliveryPlannedDate;
    }

    public function setDeliveryPlannedDate(DateTimeInterface $deliveryPlannedDate): self
    {
        $this->deliveryPlannedDate = $deliveryPlannedDate;

        return $this;
    }
}
